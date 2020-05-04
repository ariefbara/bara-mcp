<?php

namespace Query\Domain\Model\Firm\Personnel;

use Query\Domain\Model\ {
    Firm\Personnel,
    Shared\FileInfo
};

class PersonnelFileInfo
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

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
    protected $removed = false;

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
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
