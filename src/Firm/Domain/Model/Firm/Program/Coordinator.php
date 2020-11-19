<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\ {
    Model\Firm\Personnel,
    Model\Firm\Program,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\MeetingType\MeetingData,
    Service\MetricAssignmentDataProvider
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Coordinator implements CanAttendMeeting
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
    protected $removed;

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Program $program, $id, Personnel $personnel)
    {
        $this->program = $program;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->removed = false;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

    public function reassign(): void
    {
        $this->removed = false;
    }

    public function assignMetricsToParticipant(
            Participant $participant, MetricAssignmentDataProvider $metricAssignmentDataCollector): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($participant);
        $participant->assignMetrics($metricAssignmentDataCollector);
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($meetingType);
        return $meetingType->createMeeting($meetingId, $meetingData, $this);
    }

    protected function assertActive()
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active coordinator can make this request";
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

    public function canInvolvedInProgram(Program $program): bool
    {
        return !$this->removed && $this->program === $program;
    }

    public function roleCorrespondWith(ActivityParticipantType $role): bool
    {
        return $role->isCoordinatorType();
    }

    public function registerAsAttendeeCandidate(Attendee $attendee): void
    {
        $attendee->setCoordinatorAsAttendeeCandidate($this);
    }

}
