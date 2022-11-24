<?php

namespace Query\Domain\Model\User;

use Query\Application\Service\Participant\ActivityLogRepository;
use Query\Application\Service\TeamMember\OKRPeriodRepository;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\CompletedMission;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use Query\Domain\Model\User;
use Query\Domain\Service\DataFinder;
use Query\Domain\Service\Firm\Program\MentorRepository;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\LearningMaterialFinder;
use Query\Domain\Service\ObjectiveProgressReportFinder;
use Query\Domain\Task\Participant\ParticipantQueryTask;
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
    
    /**
     * 
     * @return ParticipantProfile[]
     */
    public function iterateActiveParticipantProfiles()
    {
        return $this->participant->iterateActiveParticipantProfiles();
    }

    public function getLastCompletedMission(): ?CompletedMission
    {
        return $this->participant->getLastCompletedMission();
    }

    public function getCompletedMissionCount(): int
    {
        return $this->participant->getCompletedMissionCount();
    }

    public function getActiveMissionCount(): int
    {
        return $this->participant->getActiveMissionCount();
    }

    public function viewLearningMaterial(LearningMaterialFinder $learningMaterialFinder, string $learningMaterialId): LearningMaterial
    {
        return $this->participant->viewLearningMaterial($learningMaterialFinder, $learningMaterialId);
    }

    public function pullRecordedEvents(): array
    {
        return $this->participant->pullRecordedEvents();
    }
    
    public function viewSummary(DataFinder $dataFinder): array
    {
        return $this->participant->viewSummary($dataFinder);
    }
    
    public function viewOKRPeriod(OKRPeriodRepository $okrPeriodRepository, string $okrPeriodId): OKRPeriod
    {
        return $this->participant->viewOKRPeriod($okrPeriodRepository, $okrPeriodId);
    }
    public function viewAllOKRPeriod(OKRPeriodRepository $okrPeriodRepository, int $page, int $pageSize)
    {
        return $this->participant->viewAllOKRPeriod($okrPeriodRepository, $page, $pageSize);
    }
    
    public function viewObjectiveProgressReport(ObjectiveProgressReportFinder $finder, string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $this->participant->viewObjectiveProgressReport($finder, $objectiveProgressReportId);
    }
    public function viewAllObjectiveProgressReportsInObjective(
            ObjectiveProgressReportFinder $finder, string $objectiveId, int $page, int $pageSize)
    {
        return $this->participant->viewAllObjectiveProgressReportsInObjective($finder, $objectiveId, $page, $pageSize);
    }
    
    public function viewSelfActivityLogs(ActivityLogRepository $activityLogRepository, int $page, int $pageSize)
    {
        return $this->participant->viewSelfActivityLogs($activityLogRepository, $page, $pageSize);
    }
    public function viewSharedActivityLogs(ActivityLogRepository $activityLogRepository, int $page, int $pageSize)
    {
        return $this->participant->viewSharedActivityLogs($activityLogRepository, $page, $pageSize);
    }
    
    public function viewDedicatedMentor(DedicatedMentorRepository $dedicatedMentorRepository, string $dedicatedMentorId): DedicatedMentor
    {
        return $this->participant->viewDedicatedMentor($dedicatedMentorRepository, $dedicatedMentorId);
    }
    public function viewAllDedicatedMentors(
            DedicatedMentorRepository $dedicatedMentorRepository, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        return $this->participant
                ->viewAllDedicatedMentors($dedicatedMentorRepository, $page, $pageSize, $cancelledStatus);
    }
    
    public function viewMissionComment(
            MissionCommentRepository $missionCommentRepository, string $missionCommentId): MissionComment
    {
        return $this->participant->viewMissionComment($missionCommentRepository, $missionCommentId);
    }
    public function viewAllMissionComments(
            MissionCommentRepository $missionCommentRepository, string $missionId, int $page, int $pageSize)
    {
        return $this->participant->viewAllMissionComments($missionCommentRepository, $missionId, $page, $pageSize);
    }
    
    public function viewAllMentors(MentorRepository $mentorRepository, int $page, int $pageSize)
    {
        return $this->participant->viewAllMentors($mentorRepository, $page, $pageSize);
    }
    public function viewMentor(MentorRepository $mentorRepository, string $mentorId): Consultant
    {
        return $this->participant->viewMentor($mentorRepository, $mentorId);
    }
    
    public function getUserName(): string
    {
        return $this->user->getFullName();
    }
    
    public function executeTask(ITaskExecutableByParticipant $task): void
    {
        $this->participant->executeTask($task);
    }
    
    public function executeQueryTask(ParticipantQueryTask $task, $payload): void
    {
        $this->participant->executeQueryTask($task, $payload);
    }

}
