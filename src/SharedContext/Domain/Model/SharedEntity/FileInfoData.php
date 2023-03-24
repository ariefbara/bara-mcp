<?php

namespace SharedContext\Domain\Model\SharedEntity;

class FileInfoData
{

    protected $name, $folders = [], $size;
    public string $id;
    public ?string $bucketName;
    public ?string $directory;
    public ?string $contentType;

    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    public function setBucketName(?string $bucketName)
    {
        $this->bucketName = $bucketName;
        return $this;
    }

    public function setDirectory(?string $directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function setContentType(?string $contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

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
