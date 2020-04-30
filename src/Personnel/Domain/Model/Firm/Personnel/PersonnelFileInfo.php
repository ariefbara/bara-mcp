<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel;
use Shared\Domain\Model\FileInfo;

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

    function getRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Personnel $personnel, string $id, FileInfo $fileInfo)
    {
        $this->personnel = $personnel;
        $this->id = $id;
        $this->fileInfo = $fileInfo;
        $this->removed = false;
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
