<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting;

use Notification\Domain\Model\Firm\Manager\ManagerMeetingAttendee;
use Notification\Domain\Model\Firm\Program\Consultant\ConsultantMeetingAttendee;
use Notification\Domain\Model\Firm\Program\Coordinator\CoordinatorMeetingAttendee;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Notification\Domain\Model\Firm\Program\Participant\ParticipantMeetingAttendee;

class MeetingAttendee
{
    /**
     *
     * @var Meeting
     */
    protected $meeting;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $anInitiator;

    /**
     *
     * @var bool|null
     */
    protected $willAttend;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var ManagerMeetingAttendee|null
     */
    protected $managerAttendee;

    /**
     *
     * @var CoordinatorMeetingAttendee|null
     */
    protected $coordinatorAttendee;

    /**
     *
     * @var ConsultantMeetingAttendee|null
     */
    protected $consultantAttendee;

    /**
     *
     * @var ParticipantMeetingAttendee|null
     */
    protected $participantAttendee;
    
    protected function __construct()
    {
        
    }
    
    public function addInvitationSentNotification(): void
    {
        
    }

    public function addInvitationCancelledNotification(): void
    {
        
    }
}
