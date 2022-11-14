<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\SharedModel\Note;

class ParticipantNote
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
     * @var Note
     */
    protected $note;

    protected function __construct()
    {
        
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNote(): Note
    {
        return $this->note;
    }

    //
    public function getContent(): string
    {
        return $this->note->getContent();
    }

    public function getCreatedTime(): DateTimeImmutable
    {
        return $this->note->getCreatedTime();
    }

    public function getModifiedTime(): DateTimeImmutable
    {
        return $this->note->getModifiedTime();
    }

    public function isRemoved(): bool
    {
        return $this->note->isRemoved();
    }

}
