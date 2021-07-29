<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation\AsMeetingInitiator;

use Config\EventList;
use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\ExecuteTaskAsMemberOfTeamParticipantMeetingInitiator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Task\MeetingInitiator\UpdateMeetingTask;
use Notification\Application\Listener\MeetingScheduleChangedListener;
use Notification\Application\Service\GenerateMeetingScheduleChangedNotification;
use Query\Application\Service\Firm\Team\ProgramParticipation\ViewInvitationForTeamParticipant;
use Query\Domain\Model\Firm\Program\Activity;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Resources\Application\Event\Dispatcher;

class MeetingController extends AsMeetingInitiatorBaseController
{

    public function update($teamId, $teamProgramParticipationId, $initiatorId)
    {
        
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamParticipant::class);
        $participantAttendeeRepository = $this->em->getRepository(ParticipantAttendee::class);
        
        $service = new ExecuteTaskAsMemberOfTeamParticipantMeetingInitiator(
                $teamMemberRepository, $teamParticipantRepository, $participantAttendeeRepository);
        
        $task = new UpdateMeetingTask($this->getMeetingData(), $dispatcher = $this->getUpdateMeetingScheduleDispatcher());
        
        $service->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $initiatorId, $task);
        $dispatcher->execute();

        $viewService = $this->buildViewService();
        $meeting = $viewService->showById($this->firmId(), $teamId, $initiatorId)->getActivity();
        return $this->singleQueryResponse($this->arrayDataOfMeeting($meeting));
    }
    
    protected function getMeetingData()
    {
        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $endTime = $this->dateTimeImmutableOfInputRequest("endTime");
        $location = $this->stripTagsInputRequest("location");
        $note = $this->stripTagsInputRequest("note");

        return new MeetingData($name, $description, $startTime, $endTime, $location, $note);
    }
    
    protected function getUpdateMeetingScheduleDispatcher()
    {
        $meetingRepository = $this->em->getRepository(\Notification\Domain\Model\Firm\Program\MeetingType\Meeting::class);
        $generateMeetingScheduleChangeNotification = new GenerateMeetingScheduleChangedNotification($meetingRepository);
        $sendImmediateMail = $this->buildSendImmediateMail();
        $listener = new MeetingScheduleChangedListener($generateMeetingScheduleChangeNotification, $sendImmediateMail);
        
        $dispatcher = new Dispatcher(false);
        $dispatcher->addListener(EventList::MEETING_SCHEDULE_CHANGED, $listener);
        return $dispatcher;
    }

    protected function arrayDataOfMeeting(Activity $meeting): array
    {
        return [
            "id" => $meeting->getId(),
            "name" => $meeting->getName(),
            "description" => $meeting->getDescription(),
            "startTime" => $meeting->getStartTimeString(),
            "endTime" => $meeting->getEndTimeString(),
            "location" => $meeting->getLocation(),
            "note" => $meeting->getNote(),
            "cancelled" => $meeting->isCancelled(),
            "createdTime" => $meeting->getCreatedTimeString(),
        ];
    }

    protected function buildViewService()
    {
        $participantInvitationRepository = $this->em->getRepository(ParticipantInvitee::class);
        return new ViewInvitationForTeamParticipant($participantInvitationRepository);
    }

}
