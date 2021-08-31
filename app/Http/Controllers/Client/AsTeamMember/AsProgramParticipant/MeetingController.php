<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant;

use Config\EventList;
use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\InitiateMeeting;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Firm\Domain\Model\Firm\Team\Member;
use Notification\Application\Listener\MeetingCreatedListener;
use Notification\Application\Service\GenerateMeetingCreatedNotification;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting as Meeting2;
use Query\Application\Service\Firm\Program\ViewActivity;
use Query\Domain\Model\Firm\Program\Activity;
use Resources\Application\Event\Dispatcher;

class MeetingController extends AsProgramParticipantBaseController
{

    public function initiate($teamId, $programId)
    {
        $service = $this->buildInitiateService();
        $activityTypeId = $this->stripTagsInputRequest("meetingTypeId");
        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $endTime = $this->dateTimeImmutableOfInputRequest("endTime");
        $location = $this->stripTagsInputRequest("location");
        $note = $this->stripTagsInputRequest("note");

        $meetingData = new MeetingData($name, $description, $startTime, $endTime, $location, $note);

        $meetingId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $programId, $activityTypeId, $meetingData);

        $viewService = $this->buildViewService();
        $meeting = $viewService->showById($this->firmId(), $programId, $meetingId);
        
        $this->sendAndCloseConnection($this->arrayDataOfMeeting($meeting), 201);
        $this->sendImmediateMail();
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

    protected function buildInitiateService()
    {
        $meetingRepository = $this->em->getRepository(Meeting::class);
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamParticipant::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingCreatedListenerToDispatcher($dispatcher);

        return new InitiateMeeting(
                $meetingRepository, $teamMemberRepository, $teamParticipantRepository, $activityTypeRepository, $dispatcher);
    }
    protected function addMeetingCreatedListenerToDispatcher(Dispatcher $dispatcher): void
    {
        $meetingRepository = $this->em->getRepository(Meeting2::class);
        $generateMeetingCreaterNotification = new GenerateMeetingCreatedNotification($meetingRepository);
        $sendImmediateMail = $this->buildSendImmediateMail();
        $listener =  new MeetingCreatedListener($generateMeetingCreaterNotification);
        $dispatcher->addListener(EventList::MEETING_CREATED, $listener);
    }

    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(Activity::class);
        return new ViewActivity($activityRepository);
    }

}
