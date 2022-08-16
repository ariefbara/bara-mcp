<?php

namespace Firm\Domain\Model\Firm\Program;

use Config\EventList;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Firm\Domain\Model\Firm\Program\Participant\Evaluation;
use Firm\Domain\Model\Firm\Program\Participant\EvaluationData;
use Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use Firm\Domain\Service\MetricAssignmentDataProvider;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\ValueObject\ParticipantStatus;

class Participant extends EntityContainEvents implements AssetInProgram, CanAttendMeeting
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
     * @var ParticipantStatus
     */
    protected $status;
    
    /**
     * 
     * @var int|null
     */
    protected $programPrice;

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

    /**
     * 
     * @var ArrayCollection
     */
    protected $profiles;

    /**
     * 
     * @var ArrayCollection
     */
    protected $meetingInvitations;

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
     * @var ArrayCollection
     */
    protected $dedicatedMentors;

    public function getId(): string
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function __construct(Program $program, string $id, bool $programAutoAccept, ?int $programPrice)
    {
        $this->program = $program;
        $this->id = $id;
        $this->programPrice = $programPrice;
        $this->status = new ParticipantStatus($programAutoAccept, $programPrice);

        $event = new CommonEvent(EventList::PROGRAM_APPLICATION_RECEIVED, $this->id);
        $this->recordEvent($event);
        
        if ($this->status->statusEquals(ParticipantStatus::SETTLEMENT_REQUIRED)) {
            $settlementRequiredEvent = New CommonEvent(EventList::SETTLEMENT_REQUIRED, $this->id);
            $this->recordEvent($settlementRequiredEvent);
        }
    }
    
    public function acceptRegistrant(): void
    {
        $this->status = $this->status->acceptRegistrant($this->programPrice);
        if ($this->status->statusEquals(ParticipantStatus::SETTLEMENT_REQUIRED)) {
            $event = new CommonEvent(EventList::SETTLEMENT_REQUIRED, $this->id);
            $this->recordEvent($event);
        }
    }
    
    public function rejectRegistrant(): void
    {
        $this->status = $this->status->rejectRegistrant();
    }
    
    public function qualify(): void
    {
        $this->status = $this->status->qualify();
    }

    public function assertActive(): void
    {
        if (!$this->status->statusEquals(ParticipantStatus::ACTIVE)) {
            throw RegularException::forbidden('inactive participant');
        }
    }

    public function assertAssetAccessible(AssetInProgram $asset): void
    {
        if (!$asset->belongsToProgram($this->program)) {
            throw RegularException::forbidden('forbidden: unable to access asset not in same program');
        }
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }

    public function receiveEvaluation(
            EvaluationPlan $evaluationPlan, EvaluationData $evaluationData, Coordinator $coordinator): void
    {
        $this->assertActive();
        $p = function (Evaluation $evaluation) use ($evaluationPlan) {
            return $evaluation->isCompletedEvaluationForPlan($evaluationPlan);
        };
        if (!empty($this->evaluations->filter($p)->count())) {
            $errorDetail = "forbidden: participant already completed evaluation for this plan";
            throw RegularException::forbidden($errorDetail);
        }
        $id = Uuid::generateUuid4();
        $evaluation = new Evaluation($this, $id, $evaluationPlan, $evaluationData, $coordinator);
        $this->evaluations->add($evaluation);
    }
    
    public function fail(): void
    {
        $this->status = $this->status->fail();
    }
    
    public function assignMetrics(MetricAssignmentDataProvider $metricAssignmentDataProvider): void
    {
        if (!empty($this->metricAssignment)) {
            $this->metricAssignment->update($metricAssignmentDataProvider);
        } else {
            $id = Uuid::generateUuid4();
            $this->metricAssignment = new MetricAssignment($this, $id, $metricAssignmentDataProvider);
        }
    }

    public function belongsInTheSameProgramAs(Metric $metric): bool
    {
        return $metric->belongsToProgram($this->program);
    }

    public function addProfile(ProgramsProfileForm $programsProfileForm, FormRecord $formRecord): void
    {
        $id = $formRecord->getId();
        $profile = new ParticipantProfile($this, $id, $programsProfileForm, $formRecord);
        $this->profiles->add($profile);
    }

    public function dedicateMentor(Consultant $consultant): string
    {
        $this->assertActive();
        $p = function (DedicatedMentor $dedicatedMentor) use ($consultant) {
            return $dedicatedMentor->consultantEquals($consultant);
        };
        $dedicatedMentor = $this->dedicatedMentors->filter($p)->first();
        if (empty($dedicatedMentor)) {
            $dedicatedMentor = new DedicatedMentor($this, Uuid::generateUuid4(), $consultant);
            $this->dedicatedMentors->add($dedicatedMentor);
        } else {
            $dedicatedMentor->reassign();
        }
        return $dedicatedMentor->getId();
    }

    public function initiateMeeting(string $meetingId, ActivityType $activityType, MeetingData $meetingData): Meeting
    {
        $this->assertActive();
        $activityType->assertUsableInProgram($this->program);

        $meeting = $activityType->createMeeting($meetingId, $meetingData);

        $id = Uuid::generateUuid4();
        $participantAttendee = new ParticipantAttendee($this, $id, $meeting, true);
        $this->meetingInvitations->add($participantAttendee);

        return $meeting;
    }

    public function inviteToMeeting(Meeting $meeting): void
    {
        $this->assertActive();
        $meeting->assertUsableInProgram($this->program);

        $p = function (ParticipantAttendee $participantAttendee) use ($meeting) {
            return $participantAttendee->isActiveAttendeeOfMeeting($meeting);
        };
        if (empty($this->meetingInvitations->filter($p)->count())) {
            $id = Uuid::generateUuid4();
            $participantAttendee = new ParticipantAttendee($this, $id, $meeting, false);
            $this->meetingInvitations->add($participantAttendee);
        }
    }

    public function correspondWithProgram(Program $program): bool
    {
        return $this->program === $program;
    }
    
    public function isActiveParticipantOrRegistrantOfProgram(Program $program): bool
    {
        return $this->status->isActiveRegistrantOrParticipant() && $this->program === $program;
    }
    
    public function assertManageableInProgram(Program $program): void
    {
        if ($this->program !== $program) {
            throw RegularException::forbidden('unmanaged participant');
        }
    }
    
}
