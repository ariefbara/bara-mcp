<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\Personnel\ProgramConsultation\ProgramConsultationBaseController;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot\BookedMentoringSlot as BookedMentoringSlot2;
use Personnel\Domain\Service\PersonnelFileInfoFinder;
use Personnel\Domain\Task\Mentor\CancelBookedMentoringSlotTask;
use Personnel\Domain\Task\Mentor\SubmitMentoringReportPayload;
use Personnel\Domain\Task\Mentor\SubmitBookedMentoringSlotReportTask;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\SharedModel\Mentoring\MentorReport;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;
use Query\Domain\Task\Personnel\ViewBookedMentoringSlotTask;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class BookedMentoringController extends ProgramConsultationBaseController
{
    
    public function cancel($consultantId, $id)
    {
        $bookedSlotRepository = $this->em->getRepository(BookedMentoringSlot2::class);
        $task = new CancelBookedMentoringSlotTask($bookedSlotRepository, $id);
        $this->executeMentorTaskInPersonnelContext($consultantId, $task);
        
        return $this->show($consultantId, $id);
    }
    
    public function submitReport($consultantId, $id)
    {
        $bookedSlotRepository = $this->em->getRepository(BookedMentoringSlot2::class);
        
        $participantRating = $this->stripTagsInputRequest('participantRating');
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new PersonnelFileInfoFinder($fileInfoRepository, $this->firmId(), $this->personnelId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        
        $payload = new SubmitMentoringReportPayload($id, $participantRating, $formRecordData);
        $task = new SubmitBookedMentoringSlotReportTask($bookedSlotRepository, $payload);
        $this->executeMentorTaskInPersonnelContext($consultantId, $task);
        
        return $this->show($consultantId, $id);
    }
    
    public function show($consultantId, $id)
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot::class);
        $task = new ViewBookedMentoringSlotTask($bookedMentoringSlotRepository, $id);
        $this->executePersonnelQueryTask($task);
        
        return $this->singleQueryResponse($this->arrayDataOfBookedMentoringSlot($task->result));
    }
    
    protected function arrayDataOfBookedMentoringSlot(BookedMentoringSlot $bookedMentoringSlot): array
    {
        return [
            'id' => $bookedMentoringSlot->getId(),
            'cancelled' => $bookedMentoringSlot->getCancelled(),
            'participant' => [
                'id' => $bookedMentoringSlot->getParticipant()->getId(),
                'name' => $bookedMentoringSlot->getParticipant()->getName(),
            ],
            'mentorReport' => $this->arrayDataOfMentorReport($bookedMentoringSlot->getMentorReport()),
            'participantReport' => $this->arrayDataOfParticipantReport($bookedMentoringSlot->getParticipantReport()),
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
