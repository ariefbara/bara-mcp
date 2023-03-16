<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program\Mission\LearningMaterial;
use Participant\Domain\Model\Participant;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;

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
     * @var string
     */
    protected $progressMark;

    /**
     * 
     * @var bool
     */
    protected $markAsCompleted;

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(
            Participant $participant, LearningMaterial $learningMaterial, string $id, LearningProgressData $data)
    {
        $this->participant = $participant;
        $this->learningMaterial = $learningMaterial;
        $this->id = $id;
        $this->lastModifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->progressMark = $data->getProgressMark();
        $this->markAsCompleted = $data->getMarkAsCompleted();
    }

    public function update(LearningProgressData $data): void
    {
        $this->progressMark = $data->getProgressMark();
        $this->markAsCompleted = $data->getMarkAsCompleted();
        $this->lastModifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

    public function updateProgressMark(?string $progressMark): void
    {
        $this->progressMark = $progressMark;
        $this->lastModifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

    public function markComplete(): void
    {
        if (!$this->markAsCompleted) {
            $this->markAsCompleted = true;
            $this->lastModifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        }
    }

    public function unmarkCompleteStatus(): void
    {
        if ($this->markAsCompleted) {
            $this->markAsCompleted = false;
            $this->lastModifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        }
    }

    //
    public function isAssociateWithLearningMaterial(LearningMaterial $learningMaterial): bool
    {
        return $this->learningMaterial === $learningMaterial;
    }

    //
    public function assertManageableByParticipant(Participant $participant): void
    {
        if ($this->participant !== $participant) {
            throw RegularException::forbidden('unmanaged learning progress');
        }
    }

}
