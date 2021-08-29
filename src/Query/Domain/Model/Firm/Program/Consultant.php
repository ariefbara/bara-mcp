<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Application\Service\Consultant\EvaluationPlanRepository;
use Query\Domain\Model\Firm\Personnel;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\Firm\Program\Participant\WorksheetFinder;
use Query\Domain\Service\Firm\Program\ParticipantFinder;
use Resources\Exception\RegularException;

class Consultant
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
        
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active consultant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function viewParticipant(ParticipantFinder $participantFinder, $participantId): Participant
    {
        $this->assertActive();
        return $participantFinder->findParticipantInProgram($this->program, $participantId);
    }

    public function viewAllParticipant(ParticipantFinder $participantFinder, int $page, int $pageSize,
            ?bool $activeStatus)
    {
        $this->assertActive();
        return $participantFinder->findAllParticipantInProgram($this->program, $page, $pageSize, $activeStatus);
    }

    public function viewWorksheet(WorksheetFinder $worksheetFinder, string $participantId, string $worksheetId): Worksheet
    {
        $this->assertActive();
        return $worksheetFinder->findWorksheetBelongsToParticipantInProgram(
                        $this->program, $participantId, $worksheetId);
    }

    public function viewAllWorksheets(WorksheetFinder $worksheetFinder, string $participantId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $worksheetFinder->findAllWorksheetBelongsToParticipantInProgram(
                        $this->program, $participantId, $page, $pageSize);
    }

    public function viewAllRootWorksheets(
            WorksheetFinder $worksheetFinder, string $participantId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $worksheetFinder->findAllRootWorksheetBelongsToParticipantInProgram(
                        $this->program, $participantId, $page, $pageSize);
    }

    public function viewAllBrancesOfWorksheets(
            WorksheetFinder $worksheetFinder, string $participantId, string $worksheetId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $worksheetFinder->findAllBranchOfWorksheetBelongsToParticipantInProgram(
                        $this->program, $participantId, $worksheetId, $page, $pageSize);
    }

    public function viewDedicatedMentor(DedicatedMentorRepository $dedicatedMentorRepository, string $dedicatedMentorId): DedicatedMentor
    {
        return $dedicatedMentorRepository->aDedicatedMentorBelongsToConsultant($this->id, $dedicatedMentorId);
    }

    public function viewAllDedicatedMentors(
            DedicatedMentorRepository $dedicatedMentorRepository, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        return $dedicatedMentorRepository
                        ->allDedicatedMentorsBelongsToConsultant($this->id, $page, $pageSize, $cancelledStatus);
    }

    public function viewAllMissionComments(
            MissionCommentRepository $missionCommentRepository, string $missionId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $missionCommentRepository->allMissionCommentsBelongsInMission(
                $this->program->getId(), $missionId, $page, $pageSize);
    }
    public function viewMissionComment(
            MissionCommentRepository $missionCommentRepository, string $missionCommentId): MissionComment
    {
        $this->assertActive();
        return $missionCommentRepository->aMissionCommentInProgram($this->program->getId(), $missionCommentId);
    }
    
    public function viewAllEvaluationPlan(
            EvaluationPlanRepository $evaluationPlanRepository, int $page, int $pageSize, ?bool $disabledStatus)
    {
        $this->assertActive();
        return $evaluationPlanRepository
                ->listEvaluationPlansInProgram($this->program->getId(), $page, $pageSize, $disabledStatus);
    }
    public function viewEvaluationPlan(EvaluationPlanRepository $evaluationPlanRepository, string $evaluationPlanId): EvaluationPlan
    {
        $this->assertActive();
        return $evaluationPlanRepository->singleEvaluationPlanInProgram($this->program->getId(), $evaluationPlanId);
    }
    
    public function getPersonnelName(): string
    {
        return $this->personnel->getName();
    }

}
