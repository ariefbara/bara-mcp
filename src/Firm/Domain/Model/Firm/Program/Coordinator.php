<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\{
    Model\AssetBelongsToFirm,
    Model\Firm,
    Model\Firm\Personnel,
    Model\Firm\Program,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\MeetingType\MeetingData,
    Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Service\MetricAssignmentDataProvider
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

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

    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($meetingType);
        return $meetingType->createMeeting($meetingId, $meetingData, $this);
    }

    public function approveMetricAssignmentReport(MetricAssignmentReport $metricAssignmentReport): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($metricAssignmentReport);
        $metricAssignmentReport->approve();
    }

    protected function assertActive()
    {
        if (!$this->active) {
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
        return $this->active && $this->program === $program;
    }

    public function roleCorrespondWith(ActivityParticipantType $role): bool
    {
        return $role->isCoordinatorType();
    }

    public function registerAsAttendeeCandidate(Attendee $attendee): void
    {
        $attendee->setCoordinatorAsAttendeeCandidate($this);
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }

}
