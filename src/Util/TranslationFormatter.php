<?php

declare(strict_types=1);

namespace App\Util;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;
use Traversable;

class TranslationFormatter
{
    private Dumper $dumper;

    public function __construct(
        int $indentSize,
        private readonly bool $writeOutput = true,
        private readonly bool $sortOutput = true,
        private readonly bool $groupOutput = true
    ) {
        $this->dumper = new Dumper($indentSize);
    }

    public function formatFile(string $inputFilename, ?string $outputFilename = null): bool
    {
        $contentIn = Yaml::parseFile($inputFilename);
        if (!is_array($contentIn)) {
            throw new InvalidArgumentException('Contents are not an array!');
        }

        if ($this->groupOutput) {
            $contentOut = self::groupTranslations($contentIn);
        } else {
            $contentOut = iterator_to_array(self::flattenTranslations($contentIn));
        }

        if ($this->sortOutput) {
            self::sortTranslations($contentOut);
        }

        $changed = $contentOut !== $contentIn;
        if ($this->writeOutput) {
            $output = $this->dumper->dump($contentOut, PHP_INT_MAX, 0, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
            if (!str_ends_with($output, "\n")) {
                $output .= "\n";
            }
            file_put_contents($outputFilename ?? $inputFilename, $output);
        }

        return $changed;
    }

    /**
     * @param mixed $input
     */
    private static function sortTranslations(&$input): void
    {
        if (is_array($input)) {
            ksort($input);
            array_walk($input, [self::class, 'sortTranslations']);
        }
    }

    /**
     * @param string[]|string[][] $input
     *
     * @return Traversable<string, string>
     */
    private static function flattenTranslations(array $input, string $prefix = ''): Traversable
    {
        foreach ($input as $key => $value) {
            $key = $prefix . $key;
            if (is_array($value)) {
                yield from self::flattenTranslations($value, $key . '.');
            } else {
                yield $key => $value;
            }
        }
    }

    /**
     * @param string[]|string[][] $input
     *
     * @return string[]
     */
    private static function groupTranslations(array $input): array
    {
        $output = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $value = self::groupTranslations($value);
            }
            $keyParts = is_string($key) ? explode('.', $key) : [$key];
            $count = count($keyParts);
            if ($count > 1) {
                $newSlot = &$output;
                for ($i = 0; $i < $count - 1; ++$i) {
                    if (!array_key_exists($keyParts[$i], $newSlot)) {
                        $newSlot[$keyParts[$i]] = [];
                    }
                    if (is_array($newSlot[$keyParts[$i]])) {
                        $newSlot = &$newSlot[$keyParts[$i]];
                    } else {
                        throw new RuntimeException(
                            sprintf("'%s' has both message and children!", implode('.', $keyParts))
                        );
                    }
                }
                $newSlot[$keyParts[$count - 1]] = $value;
            } elseif (array_key_exists($key, $output)) {
                $output[$key] = array_merge($output[$key], $value);
            } else {
                $output[$key] = $value;
            }
        }

        return $output;
    }
}
