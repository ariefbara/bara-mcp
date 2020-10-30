<?php

namespace Query\Domain\Model\User;

use Query\Domain\ {
    Model\Firm\Program,
    Model\Firm\Program\Mission\LearningMaterial,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Participant\MetricAssignment,
    Model\User,
    Service\LearningMaterialFinder
};
use Resources\Application\Event\ContainEvents;

class UserParticipant implements ContainEvents
{

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function __construct()
    {
        ;
    }

    public function getProgram(): Program
    {
        return $this->participant->getProgram();
    }

    public function getEnrolledTimeString(): string
    {
        return $this->participant->getEnrolledTimeString();
    }

    public function isActive(): bool
    {
        return $this->participant->isActive();
    }

    public function getNote(): ?string
    {
        return $this->participant->getNote();
    }
    
    public function getMetricAssignment(): ?MetricAssignment
    {
        return $this->participant->getMetricAssignment();
    }

    public function viewLearningMaterial(LearningMaterialFinder $learningMaterialFinder, string $learningMaterialId): LearningMaterial
    {
        return $this->participant->viewLearningMaterial($learningMaterialFinder, $learningMaterialId);
    }

    public function pullRecordedEvents(): array
    {
        return $this->participant->pullRecordedEvents();
    }

}
