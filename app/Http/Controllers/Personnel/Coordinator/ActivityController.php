<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Manager\ManagerInvitee;
use Query\Domain\Model\Firm\Program\Activity;
use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\ActivityInvitationFilter;
use Query\Domain\Task\InProgram\ViewActivityDetail;
use Query\Domain\Task\InProgram\ViewAllActivityInvitationsToProgramPartipant;
use Query\Domain\Task\InProgram\ViewAllActivityInvitationsToProgramPartipantPayload;
use Query\Domain\Task\InProgram\ViewParticipantInviteeDetail;

class ActivityController extends CoordinatorBaseController
{

    public function viewAllValidInvitationsToParticipant($coordinatorId, $participantId)
    {
        $participantInviteeRepository = $this->em->getRepository(ParticipantInvitee::class);
        $task = new ViewAllActivityInvitationsToProgramPartipant($participantInviteeRepository);

        $activityInvitationFilter = (new ActivityInvitationFilter($this->getPage(), $this->getPageSize()))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setCancelledStatus(false)
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        $payload = new ViewAllActivityInvitationsToProgramPartipantPayload($participantId, $activityInvitationFilter);

        $this->executeProgramQueryTask($coordinatorId, $task, $payload);

        $result = [];
        $result['total'] = count($payload->result);
        foreach ($payload->result as $participantInvitee) {
            $result['list'][] = $this->overviewDataOfParticipantInvitee($participantInvitee);
        }
        return $this->listQueryResponse($result);
    }
    protected function overviewDataOfParticipantInvitee(ParticipantInvitee $participantInvitee): array
    {
        return [
            'id' => $participantInvitee->getId(),
            'anInitiator' => $participantInvitee->isAnInitiator(),
            'activity' => [
                'id' => $participantInvitee->getActivity()->getId(),
                'name' => $participantInvitee->getActivity()->getName(),
                'description' => $participantInvitee->getActivity()->getDescription(),
                'startTime' => $participantInvitee->getActivity()->getStartTimeString(),
                'endTime' => $participantInvitee->getActivity()->getEndTimeString(),
                'location' => $participantInvitee->getActivity()->getLocation(),
                'note' => $participantInvitee->getActivity()->getNote(),
                'cancelled' => $participantInvitee->getActivity()->isCancelled(),
            ],
        ];
    }

    public function viewParticipantInvitationDetail($coordinatorId, $id)
    {
        $participantInviteeRepository = $this->em->getRepository(ParticipantInvitee::class);
        $task = new ViewParticipantInviteeDetail($participantInviteeRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($coordinatorId, $task, $payload);

        return $this->singleQueryResponse($this->detailDataOfParticipantInvitee($payload->result));
    }
    protected function detailDataOfParticipantInvitee(ParticipantInvitee $participantInvitee): array
    {
        return [
            'id' => $participantInvitee->getId(),
            'anInitiator' => $participantInvitee->isAnInitiator(),
            'activity' => [
                'id' => $participantInvitee->getActivity()->getId(),
                'name' => $participantInvitee->getActivity()->getName(),
                'description' => $participantInvitee->getActivity()->getDescription(),
                'startTime' => $participantInvitee->getActivity()->getStartTimeString(),
                'endTime' => $participantInvitee->getActivity()->getEndTimeString(),
                'location' => $participantInvitee->getActivity()->getLocation(),
                'note' => $participantInvitee->getActivity()->getNote(),
                'cancelled' => $participantInvitee->getActivity()->isCancelled(),
            ],
            'activityParticipant' => [
                'id' => $participantInvitee->getActivityParticipant()->getId(),
                'reportForm' => $this->arrayDataOfFeedbackForm($participantInvitee->getActivityParticipant()->getReportForm()),
            ],
            'report' => $this->arrayDataOfReport($participantInvitee->getReport()),
        ];
    }
    protected function arrayDataOfFeedbackForm(?FeedbackForm $feedbackForm): ?array
    {
        if (empty($feedbackForm)) {
            return null;
        }
        return (new FormToArrayDataConverter())->convert($feedbackForm);
    }
    protected function arrayDataOfReport(?InviteeReport $report): ?array
    {
        if (empty($report)) {
            return null;
        }
        return (new FormRecordToArrayDataConverter())->convert($report);
    }
    
    //
    public function viewActivityDetail($coordinatorId, $activityId)
    {
        $activityRepository = $this->em->getRepository(Activity::class);
        $task = new ViewActivityDetail($activityRepository);
        $payload = new CommonViewDetailPayload($activityId);
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        
        return $this->singleQueryResponse($this->detailOfActivity($payload->result));
    }
    protected function detailOfActivity(Activity $activity): array
    {
        $inviteeList = [];
        foreach ($activity->iterateActiveInviteeList() as $invitee) {
            $inviteeList[] = $this->detailOfInvitee($invitee);
        }
        
        return [
            'id' => $activity->getId(),
            'cancelled' => $activity->isCancelled(),
            'name' => $activity->getName(),
            'description' => $activity->getDescription(),
            'startTime' => $activity->getStartTimeString(),
            'endTime' => $activity->getEndTimeString(),
            'location' => $activity->getLocation(),
            'note' => $activity->getNote(),
            'createdTime' => $activity->getCreatedTimeString(),
            'inviteeList' => $inviteeList,
        ];
    }
    protected function detailOfInvitee(Activity\Invitee $invitee): array
    {
        return [
            'id' => $invitee->getId(),
            'cancelled' => $invitee->isCancelled(),
            'anInitiator' => $invitee->isAnInitiator(),
            'reportForm' => $this->arrayDataOfFeedbackForm($invitee->getActivityParticipant()->getReportForm()),
            'report' => $this->arrayDataOfReport($invitee->getReport()),
            'manager' => $this->detailOfManager($invitee->getManagerInvitee()),
            'coordinator' => $this->detailOfCoordinator($invitee->getCoordinatorInvitee()),
            'consultant' => $this->detailOfConsultant($invitee->getConsultantInvitee()),
            'participant' => $this->detailOfParticipant($invitee->getParticipantInvitee()),
        ];
    }
    protected function detailOfManager(?ManagerInvitee $managerInvitee): ?array
    {
        return empty($managerInvitee) ? null : [
            'id' => $managerInvitee->getManager()->getId(),
            'name' => $managerInvitee->getManager()->getName(),
        ];
    }
    protected function detailOfCoordinator(?CoordinatorInvitee $coordinatorInvitee): ?array
    {
        return empty($coordinatorInvitee) ? null : [
            'id' => $coordinatorInvitee->getCoordinator()->getId(),
            'personnel' => [
                'id' => $coordinatorInvitee->getCoordinator()->getPersonnel()->getId(),
                'name' => $coordinatorInvitee->getCoordinator()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function detailOfConsultant(?ConsultantInvitee $consultantInvitee): ?array
    {
        return empty($consultantInvitee) ? null : [
            'id' => $consultantInvitee->getConsultant()->getId(),
            'personnel' => [
                'id' => $consultantInvitee->getConsultant()->getPersonnel()->getId(),
                'name' => $consultantInvitee->getConsultant()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function detailOfParticipant(?ParticipantInvitee $participantInvitee): ?array
    {
        return empty($participantInvitee) ? null : [
            'id' => $participantInvitee->getParticipant()->getId(),
            'client' => $this->detailOfClient($participantInvitee->getParticipant()->getClientParticipant()),
            'team' => $this->detailOfTeam($participantInvitee->getParticipant()->getTeamParticipant()),
            'user' => $this->detailOfUser($participantInvitee->getParticipant()->getUserParticipant()),
        ];
    }
    protected function detailOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function detailOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }
    protected function detailOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }

}
