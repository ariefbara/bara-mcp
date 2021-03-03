<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Application\Service\Coordinator\ObjectiveProgressReportRepository;
use Query\Application\Service\Coordinator\OKRPeriodRepository;
use Query\Domain\Model\Firm\Personnel;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Resources\Exception\RegularException;

class Coordinator
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
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var bool
     */
    protected $active;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function isActive(): bool
    {
        return $this->active;
    }

    protected function __construct()
    {
        ;
    }

    protected function assertActive()
    {
        if (!$this->active) {
            throw RegularException::forbidden('forbidden: only active coordinator can make this request');
        }
    }

    public function viewOKRPeriod(OKRPeriodRepository $okrPeriodRepository, string $okrPeriodId): OKRPeriod
    {
        $this->assertActive();
        return $okrPeriodRepository->anOKRPeriodInProgram($this->program->getId(), $okrPeriodId);
    }
    public function viewAllOKRPeriodBelongsToParticipant(
            OKRPeriodRepository $okrPeriodRepository, string $participantId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $okrPeriodRepository->allOKRPeriodsBelongsToParticipantInProgram(
                        $this->program->getId(), $participantId, $page, $pageSize);
    }

    public function viewObjectiveProgressReport(
            ObjectiveProgressReportRepository $objectiveProgressReportRepository, string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $objectiveProgressReportRepository->anObjectiveProgressReportInProgram(
                $this->program->getId(), $objectiveProgressReportId);
    }
    public function viewAllObjectiveProgressReportBelongsToObjective(
            ObjectiveProgressReportRepository $objectiveProgressReportRepository, string $objectiveId, int $page,
            int $pageSize)
    {
        return $objectiveProgressReportRepository->allObjectiveProgressReportsBelongsToObjectiveInProgram(
                $this->program->getId(), $objectiveId, $page, $pageSize);
    }

}
