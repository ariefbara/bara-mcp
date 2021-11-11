<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\Personnel\ProgramConsultation\ProgramConsultationBaseController;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotData;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Task\Mentor\CancelMentoringSlotTask;
use Personnel\Domain\Task\Mentor\CreateMultipleMentoringSlotPayload;
use Personnel\Domain\Task\Mentor\CreateMultipleMentoringSlotTask;
use Personnel\Domain\Task\Mentor\UpdateMentoringSlotPayload;
use Personnel\Domain\Task\Mentor\UpdateMentoringSlotTask;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot as MentoringSlot2;
use Query\Domain\SharedModel\Mentoring\MentorReport;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotFilter;
use Query\Domain\Task\Personnel\ViewAllMentoringSlotPayload;
use Query\Domain\Task\Personnel\ViewAllMentoringSlotsTask;
use Query\Domain\Task\Personnel\ViewMentoringSlotTask;
use SharedContext\Domain\ValueObject\ScheduleData;

class MentoringSlotController extends ProgramConsultationBaseController
{
    public function createMultipleSlot($consultantId)
    {
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        
        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $payload = new CreateMultipleMentoringSlotPayload($consultationSetupId);
        foreach ($this->request->input('mentoringSlots') as $mentoringSlotRequest) {
            $startTime = $this->dateTimeImmutableOfVariable($mentoringSlotRequest['startTime']);
            $endTime = $this->dateTimeImmutableOfVariable($mentoringSlotRequest['endTime']);
            $mediaType = $this->stripTagsVariable($mentoringSlotRequest['mediaType']);
            $location = $this->stripTagsVariable($mentoringSlotRequest['location']);
            $scheduleData = new ScheduleData($startTime, $endTime, $mediaType, $location);
            $capacity = $this->integerOfVariable($mentoringSlotRequest['capacity']);
            
            $mentoringSlotData = new MentoringSlotData($scheduleData, $capacity);
            $payload->addMentoringSlotData($mentoringSlotData);
        }
        $task = new CreateMultipleMentoringSlotTask($mentoringSlotRepository, $consultationSetupRepository, $payload);
        
        $this->executeMentorTaskInPersonnelContext($consultantId, $task);
        
        return $this->commandOkResponse();
    }
    
    public function update($consultantId, $id)
    {
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot::class);
        
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $endTime = $this->dateTimeImmutableOfInputRequest('endTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $scheduleData = new ScheduleData($startTime, $endTime, $mediaType, $location);
        $capacity = $this->integerOfInputRequest('capacity');
        $mentoringSlotData = new MentoringSlotData($scheduleData, $capacity);
        
        $payload = new UpdateMentoringSlotPayload($id, $mentoringSlotData);
        $task = new UpdateMentoringSlotTask($mentoringSlotRepository, $payload);
        
        $this->executeMentorTaskInPersonnelContext($consultantId, $task);
        
        $viewMentoringSlotTask = $this->buildViewMentorinSlotTask($id);
        $this->executePersonnelQueryTask($viewMentoringSlotTask);
        return $this->singleQueryResponse($this->arrayDataOfMentoringSlot($viewMentoringSlotTask->result));
    }
    
    public function cancel($consultantId, $id)
    {
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot::class);
        $task = new CancelMentoringSlotTask($mentoringSlotRepository, $id);
        $this->executeMentorTaskInPersonnelContext($consultantId, $task);
        
        $viewMentoringSlotTask = $this->buildViewMentorinSlotTask($id);
        $this->executePersonnelQueryTask($viewMentoringSlotTask);
        return $this->singleQueryResponse($this->arrayDataOfMentoringSlot($viewMentoringSlotTask->result));
    }
    
    public function show($id)
    {
        $viewMentoringSlotTask = $this->buildViewMentorinSlotTask($id);
        $this->executePersonnelQueryTask($viewMentoringSlotTask);
        return $this->singleQueryResponse($this->arrayDataOfMentoringSlot($viewMentoringSlotTask->result));
    }
    
    public function showAll()
    {
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot2::class);
        $filter = (new MentoringSlotFilter())
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setConsultantId($this->stripTagQueryRequest('consultantId'))
                ->setConsultationSetupId($this->stripTagQueryRequest('consultationSetupId'))
                ->setCancelledStatus($this->filterBooleanOfQueryRequest('cancelledStatus'));
        
        $payload = new ViewAllMentoringSlotPayload($this->getPage(), $this->getPageSize(), $filter);
        $task = new ViewAllMentoringSlotsTask($mentoringSlotRepository, $payload);
        $this->executePersonnelQueryTask($task);
        
        $result = [];
        $result['total'] = count($task->result);
        foreach ($task->result as $mentoringSlot) {
            $result['list'][] = [
                'id' => $mentoringSlot->getId(),
                'cancelled' => $mentoringSlot->getCancelled(),
                'startTime' => $mentoringSlot->getStartTimeString(),
                'endTime' => $mentoringSlot->getEndTimeString(),
                'mediaType' => $mentoringSlot->getMediaType(),
                'location' => $mentoringSlot->getLocation(),
                'capacity' => $mentoringSlot->getCapacity(),
                'consultationSetup' => [
                    'id' => $mentoringSlot->getConsultationSetup()->getId(),
                    'name' => $mentoringSlot->getConsultationSetup()->getName(),
                ],
                'consultant' => [
                    'id' => $mentoringSlot->getMentor()->getId(),
                    'program' => [
                        'id' => $mentoringSlot->getMentor()->getProgram()->getId(),
                        'name' => $mentoringSlot->getMentor()->getProgram()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function buildViewMentorinSlotTask($id)
    {
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot2::class);
        return new ViewMentoringSlotTask($mentoringSlotRepository, $id);
    }
    
    protected function arrayDataOfMentoringSlot(MentoringSlot2 $mentoringSlot): array
    {
        $bookedSlots = [];
        foreach ($mentoringSlot->iterateActiveBookedSlots() as $bookedMentoringSlot) {
            $bookedSlots[] = $this->arrayDataOfBookedMentoringSlot($bookedMentoringSlot);
        }
        return [
            'id' => $mentoringSlot->getId(),
            'cancelled' => $mentoringSlot->getCancelled(),
            'startTime' => $mentoringSlot->getStartTimeString(),
            'endTime' => $mentoringSlot->getEndTimeString(),
            'mediaType' => $mentoringSlot->getMediaType(),
            'location' => $mentoringSlot->getLocation(),
            'capacity' => $mentoringSlot->getCapacity(),
            'consultationSetup' => [
                'id' => $mentoringSlot->getConsultationSetup()->getId(),
                'name' => $mentoringSlot->getConsultationSetup()->getName(),
            ],
            'consultant' => [
                'id' => $mentoringSlot->getMentor()->getId(),
                'program' => [
                    'id' => $mentoringSlot->getMentor()->getProgram()->getId(),
                    'name' => $mentoringSlot->getMentor()->getProgram()->getName(),
                ],
            ],
            'bookedSlots' => $bookedSlots,
        ];
    }
    protected function arrayDataOfBookedMentoringSlot(MentoringSlot2\BookedMentoringSlot $bookedMentoringSlot): array
    {
        return [
            'id' => $bookedMentoringSlot->getId(),
            'cancelled' => $bookedMentoringSlot->getCancelled(),
            'participant' => [
                'id' => $bookedMentoringSlot->getParticipant()->getId(),
                'name' => $bookedMentoringSlot->getParticipant()->getName(),
            ],
            'mentorReport' => $this->arrayDataOfMentorReport($bookedMentoringSlot->getMentorReport()),
            'participantReport' => $this->arrayDataOfParticipantReport($bookedMentoringSlot->getParticipant()),
        ];
    }
    protected function arrayDataOfMentorReport(?MentorReport $mentorReport): ?array
    {
        if (empty($mentorReport)) {
            return null;
        }
        $mentorReportData = (new FormRecordToArrayDataConverter())->convert($mentorReport);
        $mentorReportData['id'] = $mentorReport->getId();
        $mentorReportData['participantRating'] = $mentorReport->getParticipantRating();
        return $mentorReportData;
    }
    protected function arrayDataOfParticipantReport(?ParticipantReport $participantReport): ?array
    {
        if (empty($participantReport)) {
            return null;
        }
        $participantReportData = (new FormRecordToArrayDataConverter())->convert($participantReport);
        $participantReportData['id'] = $participantReport->getId();
        $participantReportData['mentorRating'] = $participantReport->getMentorRating();
        return $participantReportData;
    }
}
