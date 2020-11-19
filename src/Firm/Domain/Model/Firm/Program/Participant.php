<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Firm\Domain\ {
    Model\Firm\Program,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\Participant\MetricAssignment,
    Service\MetricAssignmentDataProvider
};
use Resources\ {
    DateTimeImmutableBuilder,
    Exception\RegularException,
    Uuid
};
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

    public function bootout(): void
    {
        if (!$this->active) {
            $errorDetail = 'forbidden: participant already inactive';
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
        $this->note = 'booted';
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

}
