<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Personnel;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Firm\Domain\Model\Firm\Program\Coordinator\CoordinatorAttendee;
use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Firm\Domain\Model\Firm\Program\Participant\EvaluationData;
use Firm\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Firm\Domain\Service\MetricAssignmentDataProvider;
use Resources\Exception\RegularException;
use Resources\Uuid;

class Coordinator implements CanAttendMeeting, AssetBelongsToFirm
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

    /**
     *
     * @var ArrayCollection
     */
    protected $meetingInvitations;
    
    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isActive(): bool
    {
        return $this->active;
    }

    function __construct(Program $program, $id, Personnel $personnel)
    {
        if (!$personnel->isActive()) {
            $errorDetail = "forbidden: only active personnel can be assigned as coordinator";
            throw RegularException::forbidden($errorDetail);
        }
        $this->program = $program;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->active = true;
    }
    
    protected function assertActive()
    {
        if (!$this->active) {
            $errorDetail = "forbidden: inactive coordinator";
            throw RegularException::forbidden($errorDetail);
        }
    }
    protected function assertAssetBelongsProgram(AssetInProgram $asset): void
    {
        if (!$asset->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: unable to manage asset of other program";
            throw RegularException::forbidden($errorDetail);
        }
    }
    protected function assertAssetManageable(AssetInProgram $asset, string $assetName): void
    {
        if (!$asset->belongsToProgram($this->program)) {
            throw RegularException::forbidden("forbidden: unable to manage $assetName");
        }
    }

    public function disable(): void
    {
        $this->active = false;
        foreach ($this->meetingInvitations->getIterator() as $invitation) {
            $invitation->disableValidInvitation();
        }
    }

    public function enable(): void
    {
        $this->active = true;
    }

    public function assignMetricsToParticipant(
            Participant $participant, MetricAssignmentDataProvider $metricAssignmentDataCollector): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($participant);
        $participant->assignMetrics($metricAssignmentDataCollector);
    }

    public function approveMetricAssignmentReport(MetricAssignmentReport $metricAssignmentReport): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($metricAssignmentReport);
        $metricAssignmentReport->approve();
    }

    public function rejectMetricAssignmentReport(MetricAssignmentReport $metricAssignmentReport, ?string $note): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($metricAssignmentReport);
        $metricAssignmentReport->reject($note);
    }

    public function evaluateParticipant(
            Participant $participant, EvaluationPlan $evaluationPlan, EvaluationData $evaluationData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($participant);
        $this->assertAssetBelongsProgram($evaluationPlan);
        $participant->receiveEvaluation($evaluationPlan, $evaluationData, $this);
    }

    public function qualifyParticipant(Participant $participant): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($participant);
        $participant->qualify();
    }

    public function changeConsultationSessionChannel(
            ConsultationSession $consultationSession, ?string $media, ?string $address): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($consultationSession);
        $consultationSession->changeChannel($media, $address);
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }
    
    public function approveOKRPeriod(OKRPeriod $okrPeriod): void
    {
        $this->assertActive();
        $this->assertAssetManageable($okrPeriod, 'okr period');
        $okrPeriod->approve();
    }
    public function rejectOKRPeriod(OKRPeriod $okrPeriod): void
    {
        $this->assertActive();
        $this->assertAssetManageable($okrPeriod, 'okr period');
        $okrPeriod->reject();
    }
    
    public function approveObjectiveProgressReport(ObjectiveProgressReport $objectiveProgressReport): void
    {
        $this->assertActive();
        $this->assertAssetManageable($objectiveProgressReport, 'objective progress report');
        $objectiveProgressReport->approve();
    }
    public function rejectObjectiveProgressReport(ObjectiveProgressReport $objectiveProgressReport): void
    {
        $this->assertActive();
        $this->assertAssetManageable($objectiveProgressReport, 'objective progress report');
        $objectiveProgressReport->reject();
    }
    
    public function dedicateMentorToParticipant(Participant $participant, Consultant $consultant): string
    {
        $this->assertActive();
        $this->assertAssetManageable($participant, 'participant');
        $this->assertAssetManageable($consultant, 'consultant');
        $dedicatedMentorId = $participant->dedicateMentor($consultant);
        return $dedicatedMentorId;
    }
    public function cancelMentorDedication(DedicatedMentor $dedicatedMentor): void
    {
        $this->assertActive();
        $this->assertAssetManageable($dedicatedMentor, 'dedicated mentor');
        $dedicatedMentor->cancel();
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $activityType, MeetingData $meetingData): Meeting
    {
        $this->assertActive();
        $activityType->assertUsableInProgram($this->program);
        
        $meeting = $activityType->createMeeting($meetingId, $meetingData);
        
        $id = Uuid::generateUuid4();
        $coordinatorAttendee = new CoordinatorAttendee($this, $id, $meeting, true);
        $this->meetingInvitations->add($coordinatorAttendee);
        
        return $meeting;
    }
    
    public function inviteToMeeting(Meeting $meeting): void
    {
        $this->assertActive();
        $meeting->assertUsableInProgram($this->program);
        $p = function (CoordinatorAttendee $coordinatorAttendee) use ($meeting) {
            return $coordinatorAttendee->isActiveAttendeeOfMeeting($meeting);
        };
        if (empty($this->meetingInvitations->filter($p)->count())) {
            $id = Uuid::generateUuid4();
            $coordinatorAttendee = new CoordinatorAttendee($this, $id, $meeting, false);
            $this->meetingInvitations->add($coordinatorAttendee);
        }
    }
    
}
