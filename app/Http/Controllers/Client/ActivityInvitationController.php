<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Client\ViewActivityInvitation;
use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Infrastructure\QueryFilter\InviteeFilter;

class ActivityInvitationController extends ClientBaseController
{
    public function showAll()
    {
        $participantInviteeRepository = $this->em->getRepository(ParticipantInvitee::class);
        $service = new ViewActivityInvitation($this->clientQueryRepository(), $participantInviteeRepository);
        
        $inviteeFilter = (new InviteeFilter())
                ->setCancelledStatus($this->filterBooleanOfQueryRequest('cancelledStatus'))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'));
        if (!empty($this->request->query('willAttendStatuses'))) {
            foreach ($this->request->query('willAttendStatuses') as $willAttendStatus) {
                $inviteeFilter->addWillAttendStatus($this->filterBooleanOfVariable($willAttendStatus));
            }
        }
        
        $invitations = $service->showAll(
                $this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize(), $inviteeFilter);
        
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
                'team' => $this->arrayDataOfTeam($invitation->getParticipant()->getTeamParticipant()),
            ],
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }
}
