<?php

namespace Query\Domain\Model\Firm\Team;

use DateTimeImmutable;
use Query\Application\Service\Firm\Client\AsTeamMember\TeamMemberActivityLogRepository;
use Query\Application\Service\TeamMember\ActivityLogRepository;
use Query\Application\Service\TeamMember\OKRPeriodRepository;
use Query\Domain\Event\LearningMaterialViewedByTeamMemberEvent;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Query\Domain\Model\Firm\Team\Member\TeamMemberActivityLog;
use Query\Domain\Service\DataFinder;
use Query\Domain\Service\Firm\ClientFinder;
use Query\Domain\Service\Firm\Program\ConsultationSetup\ConsultationRequestFinder;
use Query\Domain\Service\Firm\Program\MentorRepository;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\Firm\Program\Participant\ConsultationSessionFinder;
use Query\Domain\Service\Firm\Program\Participant\WorksheetFinder;
use Query\Domain\Service\Firm\ProgramFinder;
use Query\Domain\Service\Firm\Team\TeamFileInfoFinder;
use Query\Domain\Service\Firm\Team\TeamProgramParticipationFinder;
use Query\Domain\Service\Firm\Team\TeamProgramRegistrationFinder;
use Query\Domain\Service\LearningMaterialFinder;
use Query\Domain\Service\ObjectiveProgressReportFinder;
use Query\Domain\Service\TeamProgramParticipationFinder as TeamProgramParticipationFinder2;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;

class Member extends EntityContainEvents
{

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var string||null
     */
    protected $position;

    /**
     *
     * @var bool
     */
    protected $anAdmin;

    /**
     *
     * @var bool
     */
    protected $active;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    protected function __construct()
    {
        ;
    }

    protected function assertAnAdmin(): void
    {
        if (!$this->anAdmin) {
            $errorDetail = "forbidden: only team admin can make this requests";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this requests";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function isAnAdmin(): bool
    {
        return $this->anAdmin;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getJoinTimeString(): string
    {
        return $this->joinTime->format("Y-m-d H:i:s");
    }

    public function viewClientByEmail(ClientFinder $clientFinder, string $clientEmail): Client
    {
        $this->assertAnAdmin();
        $this->assertActive();
        return $clientFinder->findByEmail($this->client->getFirm()->getId(), $clientEmail);
    }

    public function viewTeamProgramRegistration(TeamProgramRegistrationFinder $teamProgramRegistrationFinder,
            string $teamProgramRegistrationId): TeamProgramRegistration
    {
        $this->assertActive();
        return $teamProgramRegistrationFinder
                        ->findProgramRegistrationBelongsToTeam($this->team, $teamProgramRegistrationId);
    }

    public function viewAllTeamProgramRegistration(TeamProgramRegistrationFinder $teamProgramRegistrationFinder,
            int $page, int $pageSize, ?bool $concludedStatus)
    {
        $this->assertActive();
        return $teamProgramRegistrationFinder
                        ->findAllProgramRegistrationsBelongsToTeam($this->team, $page, $pageSize, $concludedStatus);
    }

    public function viewTeamProgramParticipation(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId): TeamProgramParticipation
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId);
    }

    public function viewAllProgramParticipation(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $teamProgramParticipationFinder->findAllProgramParticipationsBelongsToTeam($this->team, $page, $pageSize);
    }

    public function viewWorksheet(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId,
            WorksheetFinder $worksheetFinder, string $worksheetId): Worksheet
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId)
                        ->viewWorksheet($worksheetFinder, $worksheetId);
    }

    public function viewAllWorksheets(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId,
            WorksheetFinder $worksheetFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId)
                        ->viewAllWorksheets($worksheetFinder, $page, $pageSize);
    }

    public function viewAllRootWorksheets(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId,
            WorksheetFinder $worksheetFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId)
                        ->viewAllRootWorksheets($worksheetFinder, $page, $pageSize);
    }

    public function viewAllBranchWorksheets(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId,
            WorksheetFinder $worksheetFinder, string $worksheetId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId)
                        ->viewAllBranchesWorksheets($worksheetFinder, $worksheetId, $page, $pageSize);
    }

    public function viewTeamFileInfo(TeamFileInfoFinder $teamFileInfoFinder, string $teamFileInfoId): TeamFileInfo
    {
        $this->assertActive();
        return $teamFileInfoFinder->findFileInfoBelongsToTeam($this->team, $teamFileInfoId);
    }

    public function viewProgram(ProgramFinder $programFinder, string $programId): Program
    {
        $this->assertActive();
        return $programFinder->findProgramAvaiableForTeam($this->team, $programId);
    }

    public function viewAllAvailablePrograms(ProgramFinder $programFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $programFinder->findAllProgramsAvailableForTeam($this->team, $page, $pageSize);
    }

    public function viewConsultationRequest(
            ConsultationRequestFinder $consultationRequestFinder, string $teamProgramParticipationId,
            string $consultationRequestId)
    {
        $this->assertActive();
        return $consultationRequestFinder->findConsultationRequestBelongsToTeam(
                        $this->team, $teamProgramParticipationId, $consultationRequestId);
    }

    public function viewAllConsultationRequest(
            ConsultationRequestFinder $consultationRequestFinder, string $teamProgramParticipationId, int $page,
            int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $this->assertActive();
        return $consultationRequestFinder->findAllConsultationRequestsBelongsToTeam(
                        $this->team, $teamProgramParticipationId, $page, $pageSize, $consultationRequestFilter);
    }

    public function viewConsultationSession(
            ConsultationSessionFinder $consultationSessionFinder, string $teamProgramParticipationId,
            string $consultationSessionId): ConsultationSession
    {
        $this->assertActive();
        return $consultationSessionFinder->findConsultationSessionBelongsToTeam(
                        $this->team, $teamProgramParticipationId, $consultationSessionId);
    }

    public function viewAllConsultationSession(
            ConsultationSessionFinder $consultationSessionFinder, string $teamProgramParticipationId, int $page,
            int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $this->assertActive();
        return $consultationSessionFinder->findAllConsultationSessionBelongsToTeam(
                        $this->team, $teamProgramParticipationId, $page, $pageSize, $consultationSessionFilter);
    }

    public function viewLearningMaterial(
            TeamProgramParticipationFinder2 $teamProgramParticipationFinder, string $programId,
            LearningMaterialFinder $learningMaterialFinder, string $learningMaterialId): LearningMaterial
    {
        $this->assertActive();
        $teamProgramParticipation = $teamProgramParticipationFinder
                ->execute($this->team, $programId);
        $learningMaterial = $teamProgramParticipation->viewLearningMaterial($learningMaterialFinder, $learningMaterialId);

        foreach ($teamProgramParticipation->pullRecordedEvents() as $triggeredByParticipant) {
            $event = new LearningMaterialViewedByTeamMemberEvent($this->id, $triggeredByParticipant);
            $this->recordEvent($event);
        }

        return $learningMaterial;
    }

    public function viewSummaryOfProgramParticipation(TeamProgramParticipation $teamProgramParticipation,
            DataFinder $dataFinder): array
    {
        $this->assertActive();
        $this->assertTeamOwnedProgramParticipation($teamProgramParticipation);
        return $teamProgramParticipation->viewSummary($dataFinder);
    }

    protected function assertTeamOwnedProgramParticipation(TeamProgramParticipation $teamProgramParticipation): void
    {
        if (!$teamProgramParticipation->teamEquals($this->team)) {
            throw RegularException::forbidden('forbidden: can only manage asset of your team');
        }
    }

    public function viewAllActiveProgramParticipationSummary(DataFinder $dataFinder, int $page, int $pageSize): array
    {
        $this->assertActive();
        return $dataFinder->summaryOfAllTeamProgramParticipations($this->team->getId(), $page, $pageSize);
    }

    public function viewAllSelfActivityLogs(
            TeamMemberActivityLogRepository $teamMemberActivityLogRepository, int $page, int $pageSize)
    {
        return $teamMemberActivityLogRepository->allActivityLogsOfTeamMember($this->id, $page, $pageSize);
    }

    public function viewSelfActivityLog(
            TeamMemberActivityLogRepository $teamMemberActivityLogRepository, string $teamMemberActivityLogId): TeamMemberActivityLog
    {
        return $teamMemberActivityLogRepository->anActivityLogOfTeamMember($this->id, $teamMemberActivityLogId);
    }

    public function viewOKRPeriodOfTeamParticipant(
            OKRPeriodRepository $okrPeriodRepository, TeamProgramParticipation $teamParticipant, $okrPeriodId): OKRPeriod
    {
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewOKRPeriod($okrPeriodRepository, $okrPeriodId);
    }

    public function viewAllOKRPeriodsOfTeamParticipant(
            OKRPeriodRepository $okrPeriodRepository, TeamProgramParticipation $teamParticipant, int $page,
            int $pageSize)
    {
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewAllOKRPeriod($okrPeriodRepository, $page, $pageSize);
    }

    public function viewObjectiveProgressReport(
            ObjectiveProgressReportFinder $finder, TeamProgramParticipation $teamParticipant,
            string $objectiveProgressReportId): OKRPeriod\Objective\ObjectiveProgressReport
    {
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewObjectiveProgressReport($finder, $objectiveProgressReportId);
    }

    public function viewAllObjectiveProgressReportInObjective(
            ObjectiveProgressReportFinder $finder, TeamProgramParticipation $teamParticipant, string $objectiveId,
            int $page, int $pageSize)
    {
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewAllObjectiveProgressReportsInObjective($finder, $objectiveId, $page, $pageSize);
    }

    public function viewAllSelfActivityLogsInProgram(
            ActivityLogRepository $activityLogRepository, string $participantId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $activityLogRepository
                        ->allMemberActivityLogsInProgram($this->id, $this->team->getId(), $participantId, $page,
                                $pageSize);
    }

    public function viewAllSharedActivityLogsInProgram(
            ActivityLogRepository $activityLogRepositoyr, string $participantId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $activityLogRepositoyr
                        ->allTeamSharedActivityLogsInProgram($this->id, $this->team->getId(), $participantId, $page,
                                $pageSize);
    }

    public function viewDedicatedMentor(
            TeamProgramParticipation $teamParticipant, DedicatedMentorRepository $dedicatedMentorRepository,
            string $dedicatedMentorId): DedicatedMentor
    {
        $this->assertActive();
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewDedicatedMentor($dedicatedMentorRepository, $dedicatedMentorId);
    }

    public function viewAllDedicatedMentor(
            TeamProgramParticipation $teamParticipant, DedicatedMentorRepository $dedicatedMentorRepository, int $page,
            int $pageSize, ?bool $cancelledStatus)
    {
        $this->assertActive();
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewAllDedicatedMentors($dedicatedMentorRepository, $page, $pageSize, $cancelledStatus);
    }
    
    public function viewMissionComment(
            TeamProgramParticipation $teamParticipant, MissionCommentRepository $missionCommentRepository, 
            string $missionCommentId): MissionComment
    {
        $this->assertActive();
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewMissionComment($missionCommentRepository, $missionCommentId);
    }
    public function viewAllMissionComments(
            TeamProgramParticipation $teamParticipant, MissionCommentRepository $missionCommentRepository, 
            string $missionId, int $page, int $pageSize)
    {
        $this->assertActive();
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewAllMissionComments($missionCommentRepository, $missionId, $page, $pageSize);
    }
    
    public function viewAllMentors(
            TeamProgramParticipation $teamParticipant, MentorRepository $mentorRepository, int $page, int $pageSize)
    {
        $this->assertActive();
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewAllMentors($mentorRepository, $page, $pageSize);
    }
    public function viewMentor(
            TeamProgramParticipation $teamParticipant, MentorRepository $mentorRepository, string $mentorId): Consultant
    {
        $this->assertActive();
        $this->assertTeamOwnedProgramParticipation($teamParticipant);
        return $teamParticipant->viewMentor($mentorRepository, $mentorId);
    }
    
    public function isActiveMemberCorrespondWithClient(Client $client): bool
    {
        return $this->active && $this->client === $client;
    }
    
    public function getClientName(): string
    {
        return $this->client->getFullName();
    }
    
    public function executeTeamParticipantTask(
            TeamProgramParticipation $teamParticipant, ITaskExecutableByParticipant $task): void
    {
        $this->assertActive();
        if (!$teamParticipant->teamEquals($this->team)) {
            throw RegularException::forbidden('forbidden: unmanaged program participation');
        }
        $teamParticipant->executeTask($task);
    }
    
    public function isActiveMemberWithinInspection(InspectedClientList $inspectedClientList): bool
    {
        return $this->active && $inspectedClientList->isInspectingClient($this->client);
    }

    public function executeProgramTask(
            TeamProgramParticipation $teamParticipant, ITaskInProgramExecutableByParticipant $task): void
    {
        if (!$this->active) {
            throw RegularException::forbidden('forbidden: only active team member can make this request');
        }
        if (!$teamParticipant->teamEquals($this->team)) {
            throw RegularException::forbidden('forbidden: can only access using owned program participation');
        }
        $teamParticipant->executeTaskInProgram($task);
    }

}
