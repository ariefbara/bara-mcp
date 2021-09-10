<?php

namespace Query\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Application\Service\Participant\ActivityLogRepository;
use Query\Application\Service\TeamMember\OKRPeriodRepository;
use Query\Domain\Event\LearningMaterialViewedByParticipantEvent;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\Evaluation;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Service\DataFinder;
use Query\Domain\Service\Firm\Program\MentorRepository;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\Firm\Program\Participant\WorksheetFinder;
use Query\Domain\Service\LearningMaterialFinder;
use Query\Domain\Service\ObjectiveProgressReportFinder;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;

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

    /**
     *
     * @var ArrayCollection
     */
    protected $evaluations;

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

    public function getName(): string
    {
        if (isset($this->userParticipant)) {
            return $this->userParticipant->getUser()->getFullName();
        } elseif (isset($this->clientParticipant)) {
            return $this->clientParticipant->getClient()->getFullName();
        } else {
            return $this->teamParticipant->getTeam()->getName();
        }
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

    public function getLastEvaluation(): ?Evaluation
    {
        $criteria = Criteria::create()
                ->orderBy(["submitTime" => "DESC"]);
        $evaluation = $this->evaluations->matching($criteria)->first();
        return empty($evaluation) ? null : $evaluation;
    }

    public function viewSummary(DataFinder $dataFinder): array
    {
        return $dataFinder->summaryOfParticipant($this->id);
    }

    public function viewOKRPeriod(OKRPeriodRepository $okrPeriodRepository, string $okrPeriodId): OKRPeriod
    {
        return $okrPeriodRepository->anOKRPeriodBelongsToParticipant($this->id, $okrPeriodId);
    }

    public function viewAllOKRPeriod(OKRPeriodRepository $okrPeriodRepository, int $page, int $pageSize)
    {
        return $okrPeriodRepository->allOKRPeriodsBelongsToParticipant($this->id, $page, $pageSize);
    }

    public function viewObjectiveProgressReport(ObjectiveProgressReportFinder $finder, string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $finder->findObjectiveProgressReportBelongsToParticipant($this->id, $objectiveProgressReportId);
    }

    public function viewAllObjectiveProgressReportsInObjective(
            ObjectiveProgressReportFinder $finder, string $objectiveId, int $page, int $pageSize)
    {
        return $finder->findAllObjectiveProgressReportInObjectiveBelongsToParticipant(
                        $this->id, $objectiveId, $page, $pageSize);
    }

    public function viewSelfActivityLogs(ActivityLogRepository $activityLogRepository, int $page, int $pageSize)
    {
        return $activityLogRepository->allParticipantActivityLogs($this->id, $page, $pageSize);
    }

    public function viewSharedActivityLogs(ActivityLogRepository $activityLogRepository, int $page, int $pageSize)
    {
        return $activityLogRepository->allSharedActivityLog($this->id, $page, $pageSize);
    }

    public function viewDedicatedMentor(DedicatedMentorRepository $dedicatedMentorRepository, string $dedicatedMentorId): DedicatedMentor
    {
        return $dedicatedMentorRepository->aDedicatedMentorBelongsToParticipant($this->id, $dedicatedMentorId);
    }

    public function viewAllDedicatedMentors(
            DedicatedMentorRepository $dedicatedMentorRepository, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        return $dedicatedMentorRepository
                        ->allDedicatedMentorsBelongsToParticipant($this->id, $page, $pageSize, $cancelledStatus);
    }

    public function viewMissionComment(
            MissionCommentRepository $missionCommentRepository, string $missionCommentId): MissionComment
    {
        $this->assertActive();
        return $missionCommentRepository->aMissionCommentInProgram($this->program->getId(), $missionCommentId);
    }

    public function viewAllMissionComments(
            MissionCommentRepository $missionCommentRepository, string $missionId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $missionCommentRepository->allMissionCommentsBelongsInMission(
                        $this->program->getId(), $missionId, $page, $pageSize);
    }
    
    public function viewAllMentors(MentorRepository $mentorRepository, int $page, int $pageSize)
    {
        $this->assertActive();
        return $mentorRepository->allMentorsAccessibleToParticipant($this->id, $page, $pageSize);
    }
    
    public function viewMentor(MentorRepository $mentorRepository, string $mentorId): Consultant
    {
        $this->assertActive();
        return $mentorRepository->aMentorInProgram($this->program->getId(), $mentorId);
    }
    
    public function getListOfClientPlusTeamName(): array
    {
        if (!empty($this->userParticipant)) {
            return [$this->userParticipant->getUserName()];
        } elseif (!empty ($this->clientParticipant)) {
            return [$this->clientParticipant->getClientName()];
        } elseif (!empty ($this->teamParticipant)) {
            return $this->teamParticipant->getListOfActiveMemberPlusTeamName();
        }
    }
    
    public function correspondWithClient(Client $client): bool
    {
        if (!empty($this->clientParticipant)) {
            return $this->clientParticipant->clientEquals($client);
        } elseif (!empty ($this->teamParticipant)) {
            return $this->teamParticipant->hasActiveMemberCorrespondWithClient($client);
        } else {
            return false;
        }
    }
    
    public function getTeamName(): ?string
    {
        return isset($this->teamParticipant) ? $this->teamParticipant->getTeamName() : null;
    }
    
    public function executeTask(ITaskExecutableByParticipant $task): void
    {
        $this->assertActive();
        $task->execute($this->id);
    }

}
