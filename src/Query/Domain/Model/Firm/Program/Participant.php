<?php

namespace Query\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Query\Domain\{
    Event\LearningMaterialViewedByParticipantEvent,
    Model\Firm\Client\ClientParticipant,
    Model\Firm\Program,
    Model\Firm\Program\Mission\LearningMaterial,
    Model\Firm\Program\Participant\MetricAssignment,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Team\TeamProgramParticipation,
    Model\User\UserParticipant,
    Service\Firm\Program\Participant\WorksheetFinder,
    Service\LearningMaterialFinder
};
use Resources\{
    Domain\Model\EntityContainEvents,
    Exception\RegularException
};

class Participant extends EntityContainEvents
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $enrolledTime;

    /**
     *
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var string||null
     */
    protected $note;

    /**
     *
     * @var ClientParticipant||null
     */
    protected $clientParticipant;

    /**
     *
     * @var UserParticipant||null
     */
    protected $userParticipant;

    /**
     *
     * @var TeamProgramParticipation||null
     */
    protected $teamParticipant;

    /**
     *
     * @var MetricAssignment|null
     */
    protected $metricAssignment;

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEnrolledTimeString(): string
    {
        return $this->enrolledTime->format('Y-m-d H:i:s');
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getClientParticipant(): ?ClientParticipant
    {
        return $this->clientParticipant;
    }

    public function getUserParticipant(): ?UserParticipant
    {
        return $this->userParticipant;
    }

    public function getTeamParticipant(): ?TeamProgramParticipation
    {
        return $this->teamParticipant;
    }

    public function getMetricAssignment(): ?MetricAssignment
    {
        return $this->metricAssignment;
    }

    protected function __construct()
    {
        
    }

    public function viewWorksheet(WorksheetFinder $worksheetFinder, string $worksheetId): Worksheet
    {
        $this->assertActive();
        return $worksheetFinder->findWorksheetBelongsToParticipant($this, $worksheetId);
    }

    public function viewAllWorksheet(WorksheetFinder $worksheetFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $worksheetFinder->findAllWorksheetsBelongsToParticipant($this, $page, $pageSize);
    }

    public function viewAllRootWorksheets(WorksheetFinder $worksheetFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $worksheetFinder->findAllRootWorksheetBelongsToParticipant($this, $page, $pageSize);
    }

    public function viewAllBranchWorksheets(
            WorksheetFinder $worksheetFinder, string $worksheetId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $worksheetFinder->findAllBranchesOfWorksheetBelongsToParticipant($this, $worksheetId, $page, $pageSize);
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active participant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function viewLearningMaterial(
            LearningMaterialFinder $learningMaterialFinder, string $learningMaterialId): LearningMaterial
    {
        $this->assertActive();
        $learningMaterial = $learningMaterialFinder->execute($this->program, $learningMaterialId);

        $event = new LearningMaterialViewedByParticipantEvent($this->id, $learningMaterial->getId());
        $this->recordEvent($event);

        return $learningMaterial;
    }

}
