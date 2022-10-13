<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Participant\Domain\DependencyModel\Firm\Client\AssetBelongsToTeamInterface;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Participant\BookedMentoringSlot;
use Participant\Domain\Model\Participant\CompletedMission;
use Participant\Domain\Model\Participant\ConsultationRequest;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\Participant\ConsultationSession;
use Participant\Domain\Model\Participant\DeclaredMentoring;
use Participant\Domain\Model\Participant\ManageableByParticipant;
use Participant\Domain\Model\Participant\MentoringRequest;
use Participant\Domain\Model\Participant\MentoringRequestData;
use Participant\Domain\Model\Participant\MetricAssignment;
use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Participant\Domain\Model\Participant\ParticipantProfile;
use Participant\Domain\Model\Participant\ViewLearningMaterialActivityLog;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\ConsultationSessionType;
use SharedContext\Domain\ValueObject\ScheduleData;

class Participant extends EntityContainEvents implements AssetBelongsToTeamInterface
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
     * @var MetricAssignment|null
     */
    protected $metricAssignment;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequests;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    /**
     *
     * @var TeamProgramParticipation|null
     */
    protected $teamProgramParticipation;

    /**
     *
     * @var UserParticipant|null
     */
    protected $clientParticipant;

    /**
     *
     * @var UserParticipant|null
     */
    protected $userParticipant;

    /**
     *
     * @var ArrayCollection
     */
    protected $completedMissions;

    /**
     * 
     * @var ArrayCollection
     */
    protected $profiles;

    /**
     * 
     * @var ArrayCollection
     */
    protected $okrPeriods;

    /**
     * 
     * @var ArrayCollection
     */
    protected $mentoringRequests;

    /**
     * 
     * @var ArrayCollection
     */
    protected $bookedMentorings;

    protected function __construct()
    {
        
    }

    function assertActive(): void
    {
        if (!$this->active) {
            throw RegularException::forbidden('forbidden: only active program participant can make this request');
        }
    }

    protected function assertAssetIsManageable(ManageableByParticipant $asset, string $assetName): void
    {
        if (!$asset->isManageableByParticipant($this)) {
            throw RegularException::forbidden("forbidden: unable to manage $assetName");
        }
    }

    public function belongsToTeam(Team $team): bool
    {
        return isset($this->teamProgramParticipation) ? $this->teamProgramParticipation->belongsToTeam($team) : false;
    }

    public function quit(): void
    {
        if (!$this->active) {
            $errorDetail = 'forbidden: participant already inactive';
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
        $this->note = 'quit';
    }

    public function submitConsultationRequest(
            string $consultationRequestId, ConsultationSetup $consultationSetup, Consultant $consultant,
            ConsultationRequestData $consultationRequestData, ?TeamMembership $teamMember = null): ConsultationRequest
    {
        if (!$consultationSetup->programEquals($this->program)) {
            $errorDetail = 'forbidden: consultation setup from different program';
            throw RegularException::forbidden($errorDetail);
        }

        if (!$consultant->programEquals($this->program)) {
            $errorDetail = 'forbidden: consultant from different program';
            throw RegularException::forbidden($errorDetail);
        }
        $consultationRequest = new ConsultationRequest(
                $this, $consultationRequestId, $consultationSetup, $consultant, $consultationRequestData, $teamMember);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        return $consultationRequest;
    }

    public function changeConsultationRequestTime(
            string $consultationRequestId, ConsultationRequestData $consultationRequestData,
            ?TeamMembership $teamMember = null): void
    {
        $consultationRequest = $this->getConsultationRequestOrDie($consultationRequestId);
        $consultationRequest->rePropose($consultationRequestData, $teamMember);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        $this->recordedEvents = $consultationRequest->pullRecordedEvents();
    }

    public function acceptOfferedConsultationRequest(
            string $consultationRequestId, string $consultationSessionId, ?TeamMembership $teamMember = null): void
    {
        $consultationRequest = $this->getConsultationRequestOrDie($consultationRequestId);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        $consultationRequest->accept();

        $consultationSession = $consultationRequest->createConsultationSession($consultationSessionId, $teamMember);
        $this->consultationSessions->add($consultationSession);

        $this->recordedEvents = $consultationSession->pullRecordedEvents();
    }

    public function createRootWorksheet(string $worksheetId, string $name, Mission $mission,
            FormRecordData $formRecordData, ?TeamMembership $teamMember = null): Worksheet
    {
        $this->assertActive();
        if (!$mission->programEquals($this->program)) {
            $errorDetail = "forbidden: can only access mission in same program";
            throw RegularException::forbidden($errorDetail);
        }

        $this->addCompletedMission($mission);

        return Worksheet::createRootWorksheet($this, $worksheetId, $name, $mission, $formRecordData, $teamMember);
    }

    public function submitBranchWorksheet(
            Worksheet $parentWorksheet, string $branchId, string $name, Mission $mission,
            FormRecordData $formRecordData, ?TeamMembership $teamMember = null): Worksheet
    {
        if (!$parentWorksheet->belongsToParticipant($this)) {
            $errorDetail = "forbidden: can manage asset belongs to other participant";
            throw RegularException::forbidden($errorDetail);
        }

        $this->addCompletedMission($mission);

        return $parentWorksheet->createBranchWorksheet($branchId, $name, $mission, $formRecordData, $teamMember);
    }

    protected function addCompletedMission(Mission $mission): void
    {

        $p = function (CompletedMission $completedMission) use ($mission) {
            return $completedMission->correspondWithMission($mission);
        };
        if (empty($this->completedMissions->filter($p)->count())) {
            $id = Uuid::generateUuid4();
            $completedMission = new CompletedMission($this, $id, $mission);
            $this->completedMissions->add($completedMission);
        }
    }

    public function isActiveParticipantOfProgram(Program $program): bool
    {
        return $this->active && $this->program === $program;
    }

    public function logViewLearningMaterialActivity(
            string $LogId, string $learningMaterialId, ?TeamMembership $teamMember = null): ViewLearningMaterialActivityLog
    {
        return new ViewLearningMaterialActivityLog($this, $LogId, $learningMaterialId, $teamMember);
    }

    public function submitMetricAssignmentReport(
            string $metricAssignmentReportId, ?DateTimeImmutable $observationTime,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): MetricAssignmentReport
    {
        $this->assertActive();
        if (!isset($this->metricAssignment)) {
            $errorDetail = "forbidden: no assignment available for report";
            throw RegularException::forbidden($errorDetail);
        }
        return $this->metricAssignment
                        ->submitReport($metricAssignmentReportId, $observationTime, $metricAssignmentReportDataProvider);
    }

    public function ownAllAttachedFileInfo(MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): bool
    {
        if (isset($this->teamProgramParticipation)) {
            return $this->teamProgramParticipation->ownAllAttachedFileInfo($metricAssignmentReportDataProvider);
        } elseif (isset($this->clientParticipant)) {
            return $this->clientParticipant->ownAllAttachedFileInfo($metricAssignmentReportDataProvider);
        } elseif (isset($this->userParticipant)) {
            return $this->userParticipant->ownAllAttachedFileInfo($metricAssignmentReportDataProvider);
        }
    }

    protected function getConsultationRequestOrDie(string $consultationRequestId): ConsultationRequest
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $consultationRequestId));
        $consultationRequest = $this->consultationRequests->matching($criteria)->first();
        if (empty($consultationRequest)) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
        return $consultationRequest;
    }

    protected function assertNoProposedConsultationRequestInCollectionConflictedWith(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationRequest $otherConsultationRequest) use ($consultationRequest) {
            return $otherConsultationRequest->isProposedConsultationRequestConflictedWith($consultationRequest);
        };
        if (!empty($this->consultationRequests->filter($p)->count())) {
            $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
            throw RegularException::conflict($errorDetail);
        }
    }

    protected function assertNoConsultationSessioninCollectionConflictedWithConsultationRequest(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->conflictedWithConsultationRequest($consultationRequest);
        };
        if (!empty($this->consultationSessions->filter($p)->count())) {
            $errorDetail = "conflict: requested time already occupied by your other consultation session";
            throw RegularException::conflict($errorDetail);
        }
    }

    public function submitProfile(ProgramsProfileForm $programsProfileForm, FormRecordData $formRecordData): void
    {
        $this->assertActive();
        if (!$programsProfileForm->programEquals($this->program)) {
            $errorDetail = "forbidden: unable to submit profile from other program's profile template";
            throw RegularException::forbidden($errorDetail);
        }

        $p = function (ParticipantProfile $profile) use ($programsProfileForm) {
            return $profile->anActiveProfileCorrespondWithProgramsProfileForm($programsProfileForm);
        };
        if (!empty($profile = $this->profiles->filter($p)->first())) {
            $profile->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $participantProfile = new ParticipantProfile($this, $id, $programsProfileForm, $formRecordData);
            $this->profiles->add($participantProfile);
        }
    }

    public function removeProfile(ParticipantProfile $participantProfile): void
    {
        $this->assertActive();
        if (!$participantProfile->belongsToParticipant($this)) {
            $errorDetail = "forbidden: can only remove owned profile";
            throw RegularException::forbidden($errorDetail);
        }
        $participantProfile->remove();
    }

    protected function assertNoExistingOKRPeriodInConflictWith(OKRPeriod $okrPeriod): void
    {
        $p = function (OKRPeriod $existingOKRPeriod) use ($okrPeriod) {
            return $existingOKRPeriod->inConflictWith($okrPeriod);
        };
        if (!empty($this->okrPeriods->filter($p)->count())) {
            throw RegularException::conflict('conflict: okr period in conflict with existing okr period');
        }
    }

    public function createOKRPeriod(string $okrPeriodId, OKRPeriodData $okrPeriodData): OKRPeriod
    {
        $this->assertActive();
        $okrPeriod = new OKRPeriod($this, $okrPeriodId, $okrPeriodData);
        $this->assertNoExistingOKRPeriodInConflictWith($okrPeriod);
        return $okrPeriod;
    }

    public function updateOKRPeriod(OKRPeriod $okrPeriod, OKRPeriodData $okrPeriodData): void
    {
        $this->assertActive();
        $this->assertAssetIsManageable($okrPeriod, 'okr period');
        $okrPeriod->update($okrPeriodData);
        $this->assertNoExistingOKRPeriodInConflictWith($okrPeriod);
    }

    public function cancelOKRPeriod(OKRPeriod $okrPeriod): void
    {
        $this->assertActive();
        $this->assertAssetIsManageable($okrPeriod, 'okr period');
        $okrPeriod->cancel();
    }

    public function submitObjectiveProgressReport(
            Objective $objective, string $objectiveProgressReportId,
            ObjectiveProgressReportData $objectiveProgressReportData): ObjectiveProgressReport
    {
        $this->assertActive();
        $this->assertAssetIsManageable($objective, 'objective');
        return $objective->submitReport($objectiveProgressReportId, $objectiveProgressReportData);
    }

    public function updateObjectiveProgressReport(
            ObjectiveProgressReport $objectiveProgressReport, ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        $this->assertActive();
        $this->assertAssetIsManageable($objectiveProgressReport, 'objective progress report');
        $objectiveProgressReport->update($objectiveProgressReportData);
    }

    public function cancelObjectiveProgressReportSubmission(ObjectiveProgressReport $objectiveProgressReport): void
    {
        $this->assertActive();
        $this->assertAssetIsManageable($objectiveProgressReport, 'objective progress report');
        $objectiveProgressReport->cancel();
    }

    public function executeParticipantTask(ITaskExecutableByParticipant $task): void
    {
        if (!$this->active) {
            throw RegularException::forbidden('forbidden: only active participant can make this request');
        }
        $task->execute($this);
    }

    public function declareConsultationSession(
            string $consultationSessionId, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeInterval $startEndTime, ConsultationChannel $channel): ConsultationSession
    {
        if (!$this->active) {
            throw RegularException::forbidden('forbidden: only active participant can make this request');
        }
        if (!$startEndTime->isAlreadyPassed()) {
            throw RegularException::forbidden('can only declared past consultation');
        }
        $consultationSetup->assertUsableInProgram($this->program);
        $consultant->assertUsableInProgram($this->program);
        $sessionType = new ConsultationSessionType(ConsultationSessionType::DECLARED_TYPE, null);
        return new ConsultationSession($this, $consultationSessionId, $consultationSetup, $consultant, $startEndTime,
                $channel, $sessionType);
    }

    public function bookMentoringSlot(string $bookedMentoringSlotId, MentoringSlot $mentoringSlot): BookedMentoringSlot
    {
        if (!$mentoringSlot->usableInProgram($this->program)) {
            throw RegularException::forbidden('forbidden: uanble to place booking on unusable mentoring slot');
        }
        if (!$mentoringSlot->canAcceptBookingFrom($this)) {
            $errorDetail = 'forbidden: unable to place booking, either its full or you already made booking';
            throw RegularException::forbidden($errorDetail);
        }
        $bookedMentoringSlot = new BookedMentoringSlot($this, $bookedMentoringSlotId, $mentoringSlot);
        return $bookedMentoringSlot;
    }

    public function requestMentoring(
            string $mentoringRequestId, Consultant $mentor, ConsultationSetup $consultationSetup,
            MentoringRequestData $mentoringRequestData): MentoringRequest
    {
        $mentor->assertUsableInProgram($this->program);
        $consultationSetup->assertUsableInProgram($this->program);
        return new MentoringRequest($this, $mentoringRequestId, $mentor, $consultationSetup, $mentoringRequestData);
    }

    public function assertNoConflictWithScheduledOrPotentialSchedule(Participant\ContainSchedule $mentoringSchedule): void
    {
        $mentoringRequestFilter = function (MentoringRequest $mentoringRequest) use ($mentoringSchedule) {
            return $mentoringRequest->aScheduledOrPotentialScheduleInConflictWith($mentoringSchedule);
        };
        $bookedMentoringFilter = function (BookedMentoringSlot $bookedMentoring) use ($mentoringSchedule) {
            return $bookedMentoring->aScheduledOrPotentialScheduleInConflictWith($mentoringSchedule);
        };
        if (!empty($this->mentoringRequests->filter($mentoringRequestFilter)->count()) || !empty($this->bookedMentorings->filter($bookedMentoringFilter)->count())
        ) {
            throw RegularException::forbidden('forbidden: schedule in conflict with existing schedule or potential schedule');
        }
    }

    public function declareMentoring(
            string $declaredMentoringId, Consultant $mentor, ConsultationSetup $consultationSetup,
            ScheduleData $scheduleData): Participant\DeclaredMentoring
    {
        $mentor->assertUsableInProgram($this->program);
        $consultationSetup->assertUsableInProgram($this->program);
        return new DeclaredMentoring($this, $declaredMentoringId, $mentor, $consultationSetup, $scheduleData);
    }

}
