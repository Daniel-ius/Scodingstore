<?php

declare(strict_types=1);

class DockerPushService {

    private const REGISTRIES = [
        'registry.gitlab.com/sc-rep/scoding/internal7/docker-images',
        'scodocker',
    ];

    private const IGNORE_DIRS = [
        '.',
        '..',
        '.git',
        '.idea'
    ];

    private const SUPPORTED_PLATFORMS = [
        'linux/amd64',
        'linux/arm64',
    ];

    public function push(): void
    {
        $directories = $this->scanDirectories();
        array_filter($directories, function(string $path){
            $images = [];
            foreach (self::REGISTRIES as $registry) {
                foreach (self::SUPPORTED_PLATFORMS as $platform) {
                    $images[] = $this->dockerCreateImage($registry, $path, $platform)->getImageName();
                }
            }

            return $images;
        });
    }

    private function dockerCreateImage(string $registry, string $path, string $platform): DockerImage
    {
        $imageNameParts = explode('/', str_replace(__DIR__.'/', '', $path));
        $tag = array_pop($imageNameParts);
        $platformParts = explode('/', $platform);
        $platformUname = end($platformParts);
        $imageName = sprintf('%s_%s:%s', implode('_', $imageNameParts), $platformUname, $tag);
        $image = new DockerImage($registry, $path, $imageName, $platform);

        return $image->create($_ENV['APP_ENV'] ?? 'prod');
    }

    /**
     * @return string[]
     */
    private function scanDirectories(string $path = __DIR__, array &$result = []): array
    {
        foreach (scandir($path) as $dir) {
            if (!is_dir($path.'/'.$dir) || in_array($dir, self::IGNORE_DIRS)) {
                continue;
            }

            if (file_exists($path.'/'.$dir.'/Dockerfile')) {
                $result[] = $path.'/'.$dir;
            }

            $this->scanDirectories($path.'/'.$dir, $result);
        }

        return $result;
    }
}

class DockerImage {
    public function __construct(
        private string $registry,
        private string $path,
        private string $imageName,
        private string $platform,
    ) {
    }

    /**
     * @return string
     */
    public function getRegistry(): string
    {
        return $this->registry;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getImageName(): string
    {
        return $this->imageName;
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function create(string $environment = 'prod'): self
    {
        if (!$this->build()) {
            throw new RuntimeException(sprintf('Failed to build image %s', $this->imageName));
        }

        var_dump($environment);
        if ($environment === 'prod') {
            if (!$this->push()) {
                throw new RuntimeException(sprintf('Failed to push image %s', $this->imageName));
            }
        }

        return $this;
    }

    private function build(): bool
    {
        $returnCode = exec("docker build --platform {$this->platform} -t {$this->registry}/{$this->imageName} {$this->path}/.");

        return $returnCode !== false;
    }

    private function push(): bool
    {
        $returnCode = exec("docker push {$this->registry}/{$this->imageName}");

        return $returnCode !== false;
    }
}

(new DockerPushService())
    ->push();