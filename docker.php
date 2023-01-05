<?php

declare(strict_types=1);

class DockerPushService
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
     * @var string[]
     */
    private const IGNORE_DIRS = [
        '.',
        '..',
        '.git',
        '.idea',
    ];

    /**
     * @var string[]
     */
    private array $options;

    /**
     * @param string[] $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function push(): void
    {
        $directories = $this->scanDirectories();
        array_filter($directories, function (string $path) {
            $images = [];
            foreach (self::REGISTRIES as $registry) {
                foreach (self::SUPPORTED_PLATFORMS as $platformUname => $platform) {
                    $images[] = $this->dockerCreateImage($registry, $path, $platformUname)->getImageName();
                }
            }

            return $images;
        });
    }

    private function dockerCreateImage(string $registry, string $path, string $platformUname): DockerImage
    {
        $imageNameParts = explode('/', str_replace(__DIR__.'/', '', $path));
        $tag = array_pop($imageNameParts);
        $imageName = sprintf('%s_%s:%s', implode('_', $imageNameParts), $platformUname, $tag);
        $image = new DockerImage($registry, $path, $imageName, $platformUname);

        return $image->create(isset($this->options['only-build']));
    }

    /**
     * @param string[] $result
     *
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

class DockerImage
{
    private string $platformUname;
    private string $imageName;
    private string $path;
    private string $registry;

    public function __construct(
        string $registry,
        string $path,
        string $imageName,
        string $platform
    ) {
        $this->registry = $registry;
        $this->path = $path;
        $this->imageName = $imageName;
        $this->platformUname = $platform;
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

    public function getPlatformUname(): string
    {
        return $this->platformUname;
    }

    public function create(bool $onlyBuild = false): self
    {
        if (!$this->build()) {
            throw new RuntimeException(sprintf('Failed to build image %s', $this->imageName));
        }

        if (!$this->push()) {
            throw new RuntimeException(sprintf('Failed to push image %s', $this->imageName));
        }

        return $this;
    }

    private function build(): bool
    {
        $dockerfile = sprintf('%s/Dockerfile', $this->path);
        if (!file_exists($dockerfile)) {
            throw new RuntimeException(sprintf('No Dockerfile found at %s path.', $this->path));
        }

        $content = file_get_contents($dockerfile);
        $replacedContent = str_replace(['{{platform}}', '{{ platform }}'], [$this->platformUname, $this->platformUname], $content);
        if (!isset(DockerPushService::SUPPORTED_PLATFORMS[$this->platformUname])) {
            throw new RuntimeException(sprintf('Unsupported platform for %s uname', $this->platformUname));
        }
        $platform = DockerPushService::SUPPORTED_PLATFORMS[$this->platformUname];

        file_put_contents($dockerfile, $replacedContent);
        exec("docker build --platform {$platform} -t {$this->registry}/{$this->imageName} {$this->path}/.", $output, $result);
        file_put_contents($dockerfile, $content);

        return $result === 0;
    }

    private function push(): bool
    {
        exec("docker push {$this->registry}/{$this->imageName}", $output, $result);

        return $result === 0;
    }
}

(new DockerPushService(getopt('o::', ['only-build::'])))
    ->push();
