<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotFilter;
use Query\Domain\Task\InProgram\ShowAllMentoringSlotPayload;
use Query\Domain\Task\InProgram\ShowAllMentoringSlotTask;
use Query\Domain\Task\InProgram\ShowMentoringSlotTask;

class MentoringSlotController extends AsTeamMemberBaseController
{
    public function showAll($teamId, $teamProgramParticipationId)
    {
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot::class);
        $filter = (new MentoringSlotFilter())
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setBookingAvailabilityStatus($this->filterBooleanOfQueryRequest('bookingAvailabilityStatus'))
                ->setCancelledStatus($this->filterBooleanOfQueryRequest('cancelledStatus'))
                ->setConsultantId($this->stripTagQueryRequest('consultantId'))
                ->setConsultationSetupId($this->stripTagQueryRequest('consultationSetupId'));
        $payload = new ShowAllMentoringSlotPayload($this->getPage(), $this->getPageSize(), $filter);
        $task = new ShowAllMentoringSlotTask($mentoringSlotRepository, $payload);
        
        $this->executeQueryInProgramTaskExecutableByTeamParticipant($teamId, $teamProgramParticipationId, $task);
        
        $result = [];
        $result['total'] = count($task->results);
        foreach ($task->results as $mentoringSlot) {
            $result['list'][] = $this->arrayDataOfMentoringSlot($mentoringSlot);
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($teamId, $teamProgramParticipationId, $id)
    {
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot::class);
        $task = new ShowMentoringSlotTask($mentoringSlotRepository, $id);
        $this->executeQueryInProgramTaskExecutableByTeamParticipant($teamId, $teamProgramParticipationId, $task);
        
        return $this->singleQueryResponse($this->arrayDataOfMentoringSlot($task->result));
    }
    
    protected function arrayDataOfMentoringSlot(MentoringSlot $mentoringSlot): array
    {
        return [
            'id' => $mentoringSlot->getId(),
            'cancelled' => $mentoringSlot->getCancelled(),
            'startTime' => $mentoringSlot->getStartTimeString(),
            'endTime' => $mentoringSlot->getEndTimeString(),
            'mediaType' => $mentoringSlot->getMediaType(),
            'location' => $mentoringSlot->getLocation(),
            'capacity' => $mentoringSlot->getCapacity(),
            'bookedSlotCount' => $mentoringSlot->activeBookedSlotCount(),
            'consultant' => [
                'id' => $mentoringSlot->getMentor()->getId(),
                'personnel' => [
                    'id' => $mentoringSlot->getMentor()->getPersonnel()->getId(),
                    'name' => $mentoringSlot->getMentor()->getPersonnel()->getName(),
                ],
            ],
            'consultationSetup' => [
                'id' => $mentoringSlot->getConsultationSetup()->getId(),
                'name' => $mentoringSlot->getConsultationSetup()->getName(),
            ],
        ];
    }
}
