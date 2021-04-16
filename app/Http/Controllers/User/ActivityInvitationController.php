<?php

namespace App\Http\Controllers\User;

use Query\Application\Service\User\ViewActivityInvitation;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

class ActivityInvitationController extends UserBaseController
{
    public function showAll()
    {
        $participantInviteeRepository = $this->em->getRepository(ParticipantInvitee::class);
        $service = new ViewActivityInvitation($this->userQueryRepository(), $participantInviteeRepository);
        
        $inviteeFilter = (new InviteeFilter())
                ->setCancelledStatus($this->filterBooleanOfQueryRequest('cancelledStatus'))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'));
        if (!empty($this->request->query('willAttendStatuses'))) {
            foreach ($this->request->query('willAttendStatuses') as $willAttendStatus) {
                $inviteeFilter->addWillAttendStatus($this->filterBooleanOfVariable($willAttendStatus));
            }
        }
        
        $invitations = $service->showAll($this->userId(), $this->getPage(), $this->getPageSize(), $inviteeFilter);
        
        $result = [];
        $result['total'] = count($invitations);
        foreach ($invitations as $invitation) {
            $result['list'][] = $this->arrayDataOfInvitation($invitation);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfInvitation(ParticipantInvitee $invitation): array
    {
        return [
            'id' => $invitation->getId(),
            'anInitiator' => $invitation->isAnInitiator(),
            'willAttend' => $invitation->isWillAttend(),
            'cancelled' => $invitation->isCancelled(),
            'reportSubmitted' => !empty($invitation->getReport()),
            'activity' => [
                'id' => $invitation->getActivity()->getId(),
                'name' => $invitation->getActivity()->getName(),
                'description' => $invitation->getActivity()->getDescription(),
                'startTime' => $invitation->getActivity()->getStartTimeString(),
                'endTime' => $invitation->getActivity()->getEndTimeString(),
                'location' => $invitation->getActivity()->getLocation(),
                'note' => $invitation->getActivity()->getNote(),
            ],
            'participant' => [
                'id' => $invitation->getParticipant()->getId(),
                'program' => [
                    'id' => $invitation->getParticipant()->getProgram()->getId(),
                    'name' => $invitation->getParticipant()->getProgram()->getName(),
                ],
            ],
        ];
    }
}
