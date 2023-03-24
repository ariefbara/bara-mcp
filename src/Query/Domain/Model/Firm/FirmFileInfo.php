<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\{
    Firm,
    Shared\FileInfo
};

class FirmFileInfo
{

    /**
     *
     * @var Firm
     */
    protected $firm;

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

    public function getFirm(): Firm
    {
        return $this->firm;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function getFileInfo(): FileInfo
    {
        return $this->fileInfo;
    }

    public function __construct()
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
