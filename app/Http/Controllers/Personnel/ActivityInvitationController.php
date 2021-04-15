<?php

namespace App\Http\Controllers\Personnel;

use Query\Application\Service\Personnel\ViewActivityInvitation;
use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

class ActivityInvitationController extends PersonnelBaseController
{
    public function showAll()
    {
        $inviteeRepository = $this->em->getRepository(Invitee::class);
        $service = new ViewActivityInvitation($this->personnelQueryRepository(), $inviteeRepository);
        
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
                $this->firmId(), $this->personnelId(), $this->getPage(), $this->getPageSize(), $inviteeFilter);
        
        $result = [];
        $result['total'] = count($invitations);
        foreach ($invitations as $invitation) {
            $result['list'][] = $this->arrayDataOfInvitation($invitation);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfInvitation(Invitee $invitation): array
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
            'consultant' => $this->arrayDataOfConsultant($invitation->getConsultantInvitee()),
            'coordinator' => $this->arrayDataOfCoordinator($invitation->getCoordinatorInvitee()),
        ];
    }
    protected function arrayDataOfConsultant(?ConsultantInvitee $consultantInvitee): ?array
    {
        return empty($consultantInvitee) ? null : [
            'id' => $consultantInvitee->getConsultant()->getId(),
            'program' => [
                'id' => $consultantInvitee->getConsultant()->getProgram()->getId(),
                'name' => $consultantInvitee->getConsultant()->getProgram()->getName(),
            ],
        ];
    }
    protected function arrayDataOfCoordinator(?CoordinatorInvitee $coordinatorInvitee): ?array
    {
        return empty($coordinatorInvitee) ? null : [
            'id' => $coordinatorInvitee->getCoordinator()->getId(),
            'program' => [
                'id' => $coordinatorInvitee->getCoordinator()->getProgram()->getId(),
                'name' => $coordinatorInvitee->getCoordinator()->getProgram()->getName(),
            ],
        ];
    }
}
