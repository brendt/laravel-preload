<?php

require_once __DIR__ . '/vendor/autoload.php';

class Preloader
{
    protected array $ignores = [];

    private static int $count = 0;

    private array $paths;

    private array $classMap;

    private array $loadedFiles = [];

    public function __construct(string ...$paths)
    {
        $this->paths = $paths;
        $this->classMap = require __DIR__ . '/vendor/composer/autoload_classmap.php';
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
            $this->loadPath(rtrim($path, '/'));

            set_include_path(get_include_path() . PATH_SEPARATOR . realpath($path));
        }

        $count = self::$count;

        echo "[Preloader] Successfully preloaded {$count} classes" . PHP_EOL;

        echo get_include_path() . PHP_EOL;

    }

    private function loadPath(string $path): void
    {
        if (is_dir($path)) {
            $this->loadDir($path);

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

            $this->loadPath("{$path}/{$file}");
        }

        closedir($handle);
    }

    private function loadClass(string $name): void
    {
        $path = $this->classMap[$name] ?? $name;

        if ($this->shouldIgnore($path)) {
            return;
        }

        $classContents = file_get_contents($path);

        preg_match_all('/use ([\w\\\\]+)/', $classContents, $uses);

        $uses = $uses[1] ?? [];

        $this->loadedFiles[$path] = true;

        foreach ($uses as $use) {
            $this->loadClass($use);
        }

        opcache_compile_file($path);

        self::$count++;

        echo "[Preloader] Class successfully preloaded: {$path}" . PHP_EOL;
    }

    private function shouldIgnore(?string $path): bool
    {
        if ($path === null) {
            return true;
        }

        if (isset($this->loadedFiles[$path])) {
            return true;
        }

        if (substr($path, -4) !== '.php') {
            return true;
        }

        foreach ($this->ignores as $ignore) {
            if (strpos($path, $ignore) === 0) {
                return true;
            }
        }

        return false;
    }
}

(new Preloader())
    ->paths(__DIR__ . '/vendor/laravel')
    ->load();

