<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\MeetingType\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Participant\Evaluation;
use Firm\Domain\Model\Firm\Program\Participant\EvaluationData;
use Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Service\MetricAssignmentDataProvider;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Participant implements AssetInProgram, CanAttendMeeting
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
     * @var TeamParticipant|null
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

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Program $program, string $id)
    {
        $this->program = $program;
        $this->id = $id;
        $this->enrolledTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->active = true;
        $this->note = null;
        
        $this->profiles = new ArrayCollection();
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }

    public static function participantForUser(Program $program, string $id, string $userId): self
    {
        $participant = new static($program, $id);
        $participant->userParticipant = new UserParticipant($participant, $id, $userId);
        return $participant;
    }

    public static function participantForClient(Program $program, string $id, string $clientId): self
    {
        $participant = new static($program, $id);
        $participant->clientParticipant = new ClientParticipant($participant, $id, $clientId);
        return $participant;
    }

    public static function participantForTeam(Program $program, string $id, string $teamId): self
    {
        $participant = new static($program, $id);
        $participant->teamParticipant = new TeamParticipant($participant, $id, $teamId);
        return $participant;
    }

    public function receiveEvaluation(
            EvaluationPlan $evaluationPlan, EvaluationData $evaluationData, Coordinator $coordinator): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: unable to evaluate inactive participant";
            throw RegularException::forbidden($errorDetail);
        }
        $id = Uuid::generateUuid4();
        $evaluation = new Evaluation($this, $id, $evaluationPlan, $evaluationData, $coordinator);
        $this->evaluations->add($evaluation);
    }
    
    public function qualify(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: unable to qualify inactive participant";
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
        $this->note = "completed";
    }
    
    public function disable(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: unable to disable inactive participant";
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
        
        foreach ($this->consultationSessions->getIterator() as $consultationSession) {
            $consultationSession->disableUpcomingSession();
        }
        foreach ($this->consultationRequests->getIterator() as $consultationRequest) {
            $consultationRequest->disableUpcomingRequest();
        }
        foreach ($this->meetingInvitations->getIterator() as $meetingInvitation) {
            $meetingInvitation->disableValidInvitation();
        }
    }

    public function reenroll(): void
    {
        if ($this->active) {
            $errorDetail = 'forbidden: already active participant';
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = true;
        $this->note = null;
    }

    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        if (isset($this->clientParticipant)) {
            return $this->clientParticipant->correspondWithRegistrant($registrant);
        }
        if (isset($this->userParticipant)) {
            return $this->userParticipant->correspondWithRegistrant($registrant);
        }
        if (isset($this->teamParticipant)) {
            return $this->teamParticipant->correspondWithRegistrant($registrant);
        }
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

    public function canInvolvedInProgram(Program $program): bool
    {
        return $this->active && $this->program === $program;
    }

    public function registerAsAttendeeCandidate(Attendee $attendee): void
    {
        $attendee->setParticipantAsAttendeeCandidate($this);
    }

    public function roleCorrespondWith(ActivityParticipantType $role): bool
    {
        return $role->isParticipantType();
    }

    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active participant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$meetingType->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: can only manage meeting type on same program";
            throw RegularException::forbidden($errorDetail);
        }
        return $meetingType->createMeeting($meetingId, $meetingData, $this);
    }

    public function belongsToTeam(Team $team): bool
    {
        return isset($this->teamParticipant) ? $this->teamParticipant->belongsToTeam($team) : false;
    }
    
    public function addProfile(ProgramsProfileForm $programsProfileForm, FormRecord $formRecord): void
    {
        $id = $formRecord->getId();
        $profile = new ParticipantProfile($this, $id, $programsProfileForm, $formRecord);
        $this->profiles->add($profile);
    }

}
