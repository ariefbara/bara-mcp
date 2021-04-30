<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Personnel;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\MeetingType\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Consultant implements CanAttendMeeting, AssetBelongsToFirm, AssetInProgram
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

    function __construct(Program $program, string $id, Personnel $personnel)
    {
        if (!$personnel->isActive()) {
            $errorDetail = "forbidden: can only assign active personnel as program mentor";
            throw RegularException::forbidden($errorDetail);
        }
        $this->program = $program;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->active = true;
    }
    protected function assertActive(): void
    {
        if (! $this->active) {
            throw RegularException::forbidden('forbidden: only active consultant can make this request');
        }
    }
    protected function assertAssetAccessible(AssetInProgram $asset, string $assetName): void
    {
        if (! $asset->belongsToProgram($this->program)) {
            throw RegularException::forbidden("forbidden: unable to access $assetName");
        }
    }
    
    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }

    public function enable(): void
    {
        $this->active = true;
    }

    public function disable(): void
    {
        $this->active = false;
        foreach ($this->meetingInvitations->getIterator() as $meetingInvitation) {
            $meetingInvitation->disableValidInvitation();
        }
        foreach ($this->consultationRequests->getIterator() as $consultationRequest) {
            $consultationRequest->disableUpcomingRequest();
        }
        foreach ($this->consultationSessions->getIterator() as $consultationSession) {
            $consultationSession->disableUpcomingSession();
        }
    }

    public function getPersonnelName(): string
    {
        return $this->personnel->getName();
    }

    public function canInvolvedInProgram(Program $program): bool
    {
        return $this->active && $this->program === $program;
    }

    public function registerAsAttendeeCandidate(Attendee $attendee): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: can only invite active consultant to meeting";
            throw RegularException::forbidden($errorDetail);
        }
        $attendee->setConsultantAsAttendeeCandidate($this);
    }

    public function roleCorrespondWith(ActivityParticipantType $role): bool
    {
        return $role->isConsultantType();
    }

    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active consultant can make this request";
            throw RegularException::forbidden($errorDetail);
        }

        if (!$meetingType->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: can only manage meeting type from same program";
            throw RegularException::forbidden($errorDetail);
        }
        return $meetingType->createMeeting($meetingId, $meetingData, $this);
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }
    
    public function submitCommentInMission(Mission $mission, string $missionCommentId, Mission\MissionCommentData $missionCommentData): MissionComment
    {
        $this->assertActive();
        $this->assertAssetAccessible($mission, 'mission');
        $missionCommentData->addRolePath('mentor', $this->id);
        return $this->personnel->submitCommentInMission($mission, $missionCommentId, $missionCommentData);
    }
    public function replyMissionComment(MissionComment $missionComment, string $replyId, Mission\MissionCommentData $missionCommentData): MissionComment
    {
        $this->assertActive();
        $this->assertAssetAccessible($missionComment, 'mission comment');
        $missionCommentData->addRolePath('mentor', $this->id);
        return $this->personnel->replyMissionComment($missionComment, $replyId, $missionCommentData);
    }

}
