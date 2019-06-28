<?php

require_once __DIR__ . '/vendor/autoload.php';

class Preloader
{
    protected array $ignores = [];

    private static int $count = 0;

    private array $paths;

    private array $classMap;

    private array $fileMap;

    public function __construct(string ...$paths)
    {
        $this->paths = $paths;
        $this->classMap = require __DIR__ . '/vendor/composer/autoload_classmap.php';
        $this->fileMap = array_flip($this->classMap);
    }

    public function paths(string ...$paths): Preloader
    {
        $this->paths = array_merge(
            $this->paths,
            $paths
        );

        return $this;
    }

    public function ignore(string ...$names): Preloader
    {
        $this->ignores = array_merge(
            $this->ignores,
            $names
        );

        return $this;
    }

    public function load(): void
    {
        foreach ($this->paths as $path) {
            $path = rtrim($path, '/');

            $this->loadPath($path);
        }

        $count = self::$count;

        echo "[Preloader] Successfully preloaded {$count} classes" . PHP_EOL;
    }

    private function loadPath(string $path): void
    {
        if (is_dir($path)) {
            $this->loadDir($path);

            return;
        }

        if (substr($path, -4) !== '.php') {
            return;
        }

        $this->loadClass($path);
    }

    private function loadDir(string $path): void
    {
        $handle = opendir($path);

        while ($file = readdir($handle)) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $this->loadPath($path . "/" . $file);
        }

        closedir($handle);
    }

    private function loadClass(string $path): void
    {
        $class = $this->fileMap[$path] ?? null;

        if ($class === null) {
            return;
        }

        if (
            interface_exists($class, false)
            || class_exists($class, false)
            || trait_exists($class, false)
            || $this->shouldIgnore($class)
        ) {
            return;
        }

        require_once($path);

        self::$count++;

        echo "[Preloader] Class successfully preloaded: {$class}" . PHP_EOL;
    }

    private function shouldIgnore(string $name): bool
    {
        foreach ($this->ignores as $ignore) {
            if (strpos($name, $ignore) === 0) {
                return true;
            }
        }

        return false;
    }
}

(new Preloader())
    ->paths(__DIR__ . '/vendor/laravel')
    ->ignore(\Illuminate\Filesystem\Cache::class)
    ->load();

