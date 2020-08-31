<?php

namespace SharedContext\Domain\Model\SharedEntity;

class FileInfoData
{

    protected $name, $folders = [], $size;

    public function __construct(string $name, ?float $size)
    {
        $this->name = $name;
        $this->size = $size;
    }

    public function addFolder(string $folder): void
    {
        $this->folders[] = $folder;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFolders(): array
    {
        return $this->folders;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }
}

