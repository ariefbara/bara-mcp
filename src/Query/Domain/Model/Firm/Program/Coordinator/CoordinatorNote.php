<?php

namespace Query\Domain\Model\Firm\Program\Coordinator;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\SharedModel\Note;

class CoordinatorNote
{

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

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

    /**
     * 
     * @var bool
     */
    protected $viewableByParticipant;

    protected function __construct()
    {
        
    }

    public function getCoordinator(): Coordinator
    {
        return $this->coordinator;
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

    public function isViewableByParticipant(): bool
    {
        return $this->viewableByParticipant;
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
