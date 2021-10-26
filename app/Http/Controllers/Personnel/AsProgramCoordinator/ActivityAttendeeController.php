<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Domain\Model\Firm\Manager\ManagerInvitee;
use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Task\InProgram\ViewActivityAttendeeTask;
use Query\Domain\Task\InProgram\ViewAllActivityAttendeesPayload;
use Query\Domain\Task\InProgram\ViewAllActivityAttendeesTask;

class ActivityAttendeeController extends AsProgramCoordinatorBaseController
{

    public function showAll($programId, $activityId)
    {
        $attendeeRepository = $this->em->getRepository(Invitee::class);
        $payload = (new ViewAllActivityAttendeesPayload($activityId, $this->getPage(), $this->getPageSize()))
                ->setAttendeedStatus($this->filterBooleanOfQueryRequest('ateendedStatus'))
                ->setCancelledStatus($this->filterBooleanOfQueryRequest('cancelledStatus'));
        
        $task = new ViewAllActivityAttendeesTask($attendeeRepository, $payload);
        $this->executeTaskInProgram($programId, $task);
        
        $result = [];
        $result['total'] = count($task->result);
        foreach ($task->result as $invitee) {
            $result['list'][] = $this->arrayDataOfInvitee($invitee);
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $attendeeId)
    {
        $attendeeRepository = $this->em->getRepository(Invitee::class);
        $task = new ViewActivityAttendeeTask($attendeeRepository, $attendeeId);
        $this->executeTaskInProgram($programId, $task);
        
        return $this->singleQueryResponse($this->arrayDataOfInvitee($task->result));
    }
    
    protected function arrayDataOfInvitee(Invitee $invitee): array
    {
        return [
            "id" => $invitee->getId(),
            "anInitiator" => $invitee->isAnInitiator(),
            "manager" => $this->arrayDataOfManager($invitee->getManagerInvitee()),
            "coordinator" => $this->arrayDataOfCoordinator($invitee->getCoordinatorInvitee()),
            "consultant" => $this->arrayDataOfConsultant($invitee->getConsultantInvitee()),
            "participant" => $this->arrayDataOfParticipant($invitee->getParticipantInvitee()),
            'report' => $this->arrayDataOfReport($invitee->getReport()),
        ];
    }
    protected function arrayDataOfManager(?ManagerInvitee $managerInvitee): ?array
    {
        return empty($managerInvitee)? null: [
            "id" => $managerInvitee->getManager()->getId(),
            "name" => $managerInvitee->getManager()->getName(),
        ];
    }
    protected function arrayDataOfCoordinator(?CoordinatorInvitee $coordinatorInvitee): ?array
    {
        return empty($coordinatorInvitee)? null: [
            "id" => $coordinatorInvitee->getCoordinator()->getId(),
            "name" => $coordinatorInvitee->getCoordinator()->getPersonnel()->getName(),
        ];
    }
    protected function arrayDataOfConsultant(?ConsultantInvitee $consultantInvitee): ?array
    {
        return empty($consultantInvitee)? null: [
            "id" => $consultantInvitee->getConsultant()->getId(),
            "name" => $consultantInvitee->getConsultant()->getPersonnel()->getName(),
        ];
    }
    protected function arrayDataOfParticipant(?ParticipantInvitee $participantInvitee): ?array
    {
        
        return empty($participantInvitee)? null: [
            "id" => $participantInvitee->getParticipant()->getId(),
            "name" => $participantInvitee->getParticipant()->getName(), 
        ];
    }
    protected function arrayDataOfReport(?InviteeReport $report): ?array
    {
        return empty($report) ? null : (new FormRecordToArrayDataConverter())->convert($report);
    }

}
