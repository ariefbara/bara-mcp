<?php

namespace Query\Domain\Model\Firm\Manager;

use Query\Domain\Model\{
    Firm\Manager,
    Shared\FileInfo
};

class ManagerFileInfo
{

    /**
     *
     * @var Manager
     */
    protected $manager;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getManager(): Manager
    {
        return $this->manager;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        
    }

    function getFolders(): array
    {
        return $this->fileInfo->getFolders();
    }

    function getName(): string
    {
        return $this->fileInfo->getName();
    }

    function getSize(): ?float
    {
        return $this->fileInfo->getSize();
    }

    public function getFullyQualifiedFileName(): string
    {
        return $this->fileInfo->getFullyQualifiedFileName();
    }

}
