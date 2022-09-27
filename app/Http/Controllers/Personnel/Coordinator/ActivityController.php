<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\ActivityInvitationFilter;
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

}
