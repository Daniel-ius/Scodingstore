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
        'vendor',
        'tests',
        'src',
    ];

    /**
     * @var string[]
     */
    private array $options;

    private Logger $logger;

    /**
     * @param string[] $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->logger = new Logger();
    }

    public function push(): void
    {
        $this->logger->log('Starting to push images...');
        $directories = $this->scanDirectories(__DIR__.'/images');
        array_filter($directories, function (string $path) {
            $images = [];
            foreach (self::REGISTRIES as $registry) {
                $images[] = $this->dockerCreateImage($registry, $path)->getImageName();
            }

            return $images;
        });

        $this->logger->log('Job completed...');
    }

    private function dockerCreateImage(string $registry, string $path): DockerImage
    {
        $imageNameParts = explode('/', str_replace(__DIR__.'/', '', $path));
        $tag = array_pop($imageNameParts);
        $imageName = sprintf('%s:%s', implode('_', $imageNameParts), $tag);
        $image = new DockerImage($registry, $path, $imageName, self::SUPPORTED_PLATFORMS);

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
    /**
     * @var string[]
     */
    private array $platforms;
    private string $imageName;
    private string $path;
    private string $registry;

    private Logger $logger;

    /**
     * @param string[] $platforms
     */
    public function __construct(
        string $registry,
        string $path,
        string $imageName,
        array $platforms
    ) {
        $this->registry = $registry;
        $this->path = $path;
        $this->imageName = $imageName;
        $this->platforms = $platforms;
        $this->logger = new Logger();
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
        $this->logger->log(sprintf('Creating %s image using $onlyBuild = %d', $this->imageName, (int) $onlyBuild));
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
        $this->logger->log(sprintf('Image %s build complete.', $this->imageName));

        if ($result !== 0) {
            throw new RuntimeException(sprintf('Failed to build image %s', $this->imageName));
        }

        return $this;
    }
}

class Logger
{
    public function log(string $message): void
    {
        echo $message."\n";
    }
}

(new DockerPushService(getopt('o::', ['only-build::'])))
    ->push();
