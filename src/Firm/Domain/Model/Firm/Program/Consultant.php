<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Personnel;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Consultant\ConsultantAttendee;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Resources\Domain\Model\ContainAggregatedEntitiesHavingEvents;
use Resources\Exception\RegularException;
use Resources\Uuid;

class Consultant extends ContainAggregatedEntitiesHavingEvents implements CanAttendMeeting, AssetBelongsToFirm, AssetInProgram
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
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $dedicatedMentees;
    
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

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }
    
    public function submitCommentInMission(Mission $mission, string $missionCommentId, MissionCommentData $missionCommentData): MissionComment
    {
        $this->assertActive();
        $this->assertAssetAccessible($mission, 'mission');
        $missionCommentData->addRolePath('mentor', $this->id);
        return $this->personnel->submitCommentInMission($mission, $missionCommentId, $missionCommentData);
    }
    public function replyMissionComment(MissionComment $missionComment, string $replyId, MissionCommentData $missionCommentData): MissionComment
    {
        $this->assertActive();
        $this->assertAssetAccessible($missionComment, 'mission comment');
        $missionCommentData->addRolePath('mentor', $this->id);
        return $this->personnel->replyMissionComment($missionComment, $replyId, $missionCommentData);
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $activityType, MeetingData $meetingData): Meeting
    {
        $this->assertActive();
        $activityType->assertUsableInProgram($this->program);
        
        $meeting = $activityType->createMeeting($meetingId, $meetingData);
        
        $id = Uuid::generateUuid4();
        $consultantAttendee = new ConsultantAttendee($this, $id, $meeting, true);
        $this->meetingInvitations->add($consultantAttendee);
        
        return $meeting;
    }

    public function inviteToMeeting(Meeting $meeting): void
    {
        $this->assertActive();
        $meeting->assertUsableInProgram($this->program);
        
        $p = function (ConsultantAttendee $consultantAttendee) use ($meeting) {
            return $consultantAttendee->isActiveAttendeeOfMeeting($meeting);
        };
        if (empty($this->meetingInvitations->filter($p)->count())) {
            $id = Uuid::generateUuid4();
            $consultantAttendee = new ConsultantAttendee($this, $id, $meeting, false);
            $this->meetingInvitations->add($consultantAttendee);
        }
    }
    
    public function inviteAllActiveDedicatedMenteesToMeeting(Meeting $meeting): void
    {
        $p = function (DedicatedMentor $dedicatedMentor) {
            return $dedicatedMentor->isActiveAssignment();
        };
        foreach ($this->dedicatedMentees->filter($p)->getIterator() as $dedicatedMentee) {
            $dedicatedMentee->inviteParticipantToMeeting($meeting);
        }
        
        $this->recordEntityHavingEvents($meeting);
    }

}
