<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\{
    Firm\Program\Participant,
    Shared\FileInfo
};

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

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getParticipant(): Participant
    {
        return $this->participant;
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
