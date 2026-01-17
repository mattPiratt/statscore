<?php

namespace App\Statistics\Infrastructure\File;

class FileStorage
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    public function save(array $data): void
    {
        $line = json_encode($data) . PHP_EOL;
        file_put_contents($this->filePath, $line, FILE_APPEND | LOCK_EX);
    }

    public function overwrite(array $data): void
    {
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    }

    public function getContent(): ?string
    {
        if (!file_exists($this->filePath)) {
            return null;
        }

        return file_get_contents($this->filePath);
    }

    public function getAll(): array
    {
        $content = $this->getContent();
        if ($content === null) {
            return [];
        }

        $lines = explode(PHP_EOL, trim($content));

        return array_map(function ($line) {
            return json_decode($line, true);
        }, array_filter($lines));
    }
}