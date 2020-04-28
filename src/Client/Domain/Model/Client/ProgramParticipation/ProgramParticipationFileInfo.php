<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation;
use Shared\Domain\Model\FileInfo;

class ProgramParticipationFileInfo
{

    /**
     *
     * @var ProgramParticipation
     */
    protected $programParticipation;

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

    function getProgramParticipation(): ProgramParticipation
    {
        return $this->programParticipation;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(ProgramParticipation $programParticipation, string $id, FileInfo $fileInfo)
    {
        $this->programParticipation = $programParticipation;
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
