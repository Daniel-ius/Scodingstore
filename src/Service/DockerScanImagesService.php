<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\DockerImage;

class DockerScanImagesService
{
    /**
     * @var string[]
     */
    public const SUPPORTED_PLATFORMS = [
        'amd64' => 'linux/amd64',
        'arm64' => 'linux/arm64',
    ];

    /**
     * @var string[]
     */
    private const REGISTRIES = [
        'registry.gitlab.scoding.com/scoding-internal/docker-images',
    ];

    /**
     * @param array{only-build: bool, path: string} $options
     */
    public function __construct(
        private readonly string $rootPath,
        private readonly array $options
    ) {
    }

    public function scan(): void
    {
        $directories = $this->scanDirectories($this->options['path']);
        array_filter($directories, function (string $path) {
            $images = [];
            foreach (self::REGISTRIES as $registry) {
                $images[] = $this->dockerCreateImage($registry, $path)->getImageName();
            }

            return $images;
        });
    }

    private function dockerCreateImage(string $registry, string $path): DockerImage
    {
        $imageNameParts = explode('/', str_replace(sprintf('%s/', $this->rootPath), '', $path));
        $tag = array_pop($imageNameParts);
        $imageName = sprintf('%s:%s', implode('_', $imageNameParts), $tag);
        $image = new DockerImage($registry, $path, $imageName, self::SUPPORTED_PLATFORMS);

        return $image->create($this->options['only-build']);
    }

    /**
     * @param string[] $result
     *
     * @return string[]
     */
    private function scanDirectories(string $path = __DIR__, array &$result = []): array
    {
        foreach (scandir($path) as $dir) {
            if ($dir === 'Dockerfile') {
                $result[] = $path;
                continue;
            }

            if (!is_dir($path . '/' . $dir) || in_array($dir, ['.', '..'])) {
                continue;
            }

            if (file_exists($path . '/' . $dir . '/Dockerfile')) {
                $result[] = $path . '/' . $dir;
            }

            $this->scanDirectories($path . '/' . $dir, $result);
        }

        return $result;
    }
}
