<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Model\Firm\Program\Participant;

class LearningProgress
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var LearningMaterial
     */
    protected $learningMaterial;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $lastModifiedTime;

    /**
     * 
     * @var string|null
     */
    protected $progressMark;

    /**
     * 
     * @var bool
     */
    protected $markAsCompleted;

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getLearningMaterial(): LearningMaterial
    {
        return $this->learningMaterial;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLastModifiedTime(): DateTimeImmutable
    {
        return $this->lastModifiedTime;
    }

    public function getProgressMark(): ?string
    {
        return $this->progressMark;
    }

    public function getMarkAsCompleted(): bool
    {
        return $this->markAsCompleted;
    }

    protected function __construct()
    {
        
    }

}
