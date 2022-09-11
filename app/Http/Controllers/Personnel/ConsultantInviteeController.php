<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantInviteeFilter;
use Query\Domain\Task\Personnel\ViewAllConsultantInviteeWithPendingReport;
use Query\Domain\Task\Personnel\ViewAllConsultantInviteeWithPendingReportPayload;
use Resources\Domain\ValueObject\QueryOrder;

class ConsultantInviteeController extends PersonnelBaseController
{

    public function allWithPendingReport()
    {
        $consultantInviteeRepository = $this->em->getRepository(ConsultantInvitee::class);
        $queryOrder = new QueryOrder($this->stripTagQueryRequest('order') ?? 'ASC');
        $consultantInviteeFilter = (new ConsultantInviteeFilter($queryOrder))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'));
        $payload = new ViewAllConsultantInviteeWithPendingReportPayload(
                $this->getPage(), $this->getPageSize(), $consultantInviteeFilter);
        $task = new ViewAllConsultantInviteeWithPendingReport($consultantInviteeRepository, $payload);
        $this->executePersonnelQueryTask($task);
        
        $result = [];
        $result['total'] = count($payload->result);
        foreach ($payload->result as $consultantInvitee) {
            $result['list'][] = $this->arrayDataOfConsultantInvitee($consultantInvitee);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfConsultantInvitee(ConsultantInvitee $consultantInvitee): array
    {
        return [
            'id' => $consultantInvitee->getId(),
            'anInitiator' => $consultantInvitee->isAnInitiator(),
            'consultant' => [
                'id' => $consultantInvitee->getConsultant()->getId(),
                'program' => [
                    'id' => $consultantInvitee->getConsultant()->getProgram()->getId(),
                    'name' => $consultantInvitee->getConsultant()->getProgram()->getName(),
                ],
            ],
            'activity' => [
                'id' => $consultantInvitee->getActivity()->getId(),
                'name' => $consultantInvitee->getActivity()->getName(),
                'startTime' => $consultantInvitee->getActivity()->getStartTimeString(),
                'endTime' => $consultantInvitee->getActivity()->getEndTimeString(),
            ],
        ];
    }

}
