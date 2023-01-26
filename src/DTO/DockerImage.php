<?php

declare(strict_types=1);

namespace App\DTO;

use RuntimeException;

class DockerImage
{
    /**
     * @param string[] $platforms
     */
    public function __construct(
        private string $registry,
        private string $path,
        private string $imageName,
    /**
     * @var string[]
     */
    private array $platforms
    ) {
    }

    public function getRegistry(): string
    {
        return $this->registry;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function create(bool $onlyBuild = false): self
    {
        $pushFlag = $onlyBuild ? '' : '--push';
        $dockerfile = sprintf('%s/Dockerfile', $this->path);
        if (!file_exists($dockerfile)) {
            throw new RuntimeException(sprintf('No Dockerfile found at %s path.', $this->path));
        }

        $platforms = implode(',', array_filter($this->platforms, fn (string $platform) => $platform));
        exec('docker buildx inspect multiarch', $output, $result);
        if ($result === 1) {
            exec('docker buildx create --name multiarch --use', $output, $result);
        }
        exec("docker buildx build --platform {$platforms} -t {$this->registry}/{$this->imageName} {$this->path}/. $pushFlag", $output, $result);

        if ($result !== 0) {
            throw new RuntimeException(sprintf('Failed to build image %s', $this->imageName));
        }

        return $this;
    }
}
