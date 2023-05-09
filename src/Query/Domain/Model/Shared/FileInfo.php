<?php

namespace Query\Domain\Model\Shared;

use Resources\Infrastructure\Persistence\Google\GoogleStorage;

class FileInfo
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var array
     */
    protected $folders = [];

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var float
     */
    protected $size = null;
    protected string $bucketName;
    protected string $objectName;
    protected ?string $contentType;

    function getId(): string
    {
        return $this->id;
    }

    function getFolders(): array
    {
        return $this->folders;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getSize(): ?float
    {
        return $this->size;
    }

    public function getBucketName(): string
    {
        return $this->bucketName;
    }

    public function getObjectName(): string
    {
        return $this->objectName;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    protected function __construct()
    {
        
    }

    public function getFullyQualifiedFileName(?GoogleStorage $googleStorage = null): string
    {
        // file stored in Google Cloud Storage
        if ($this->objectName) {
            return $googleStorage->createSignedDownloadForObjectInBucket($this->bucketName, $this->objectName);
        }
        
        // file in local storage
        $path = '';
        foreach ($this->folders as $folder) {
            $path .= DIRECTORY_SEPARATOR . $folder;
        }
        return $path . DIRECTORY_SEPARATOR . $this->name;
    }

}
