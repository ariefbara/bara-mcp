<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Shared\FileInfo;

class ParticipantFileInfo
{

    /**
     *
     * @var Participant
     */
    protected $participant;

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

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    function getId(): string
    {
        return $this->id;
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
