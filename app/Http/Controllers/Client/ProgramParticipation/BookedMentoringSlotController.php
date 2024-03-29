<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\Model\Participant\BookedMentoringSlot as BookedMentoringSlot2;
use Participant\Domain\Service\ClientFileInfoFinder;
use Participant\Domain\Task\Participant\BookMentoringSlotPayload;
use Participant\Domain\Task\Participant\BookMentoringSlotTask;
use Participant\Domain\Task\Participant\CancelBookedMentoringSlotTask;
use Participant\Domain\Task\Participant\SubmitMentoringReportPayload;
use Participant\Domain\Task\Participant\SubmitBookedMentoringReportTask;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;
use Query\Domain\Task\Participant\ShowBookedMentoringSlotTask;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class BookedMentoringSlotController extends ClientParticipantBaseController
{

    public function book($programParticipationId, $mentoringSlotId)
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot2::class);
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot::class);
        $payload = new BookMentoringSlotPayload($mentoringSlotId);

        $task = new BookMentoringSlotTask($bookedMentoringSlotRepository, $mentoringSlotRepository, $payload);
        $this->executeParticipantTask($programParticipationId, $task);

        $bookedMentoringSlot = $this->buildAndExecuteSingleQueryTask(
                        $programParticipationId, $task->bookedMentoringSlotId)->result;
        return $this->commandCreatedResponse($this->arrayDataOfBookedMentoringSlot($bookedMentoringSlot));
    }

    public function cancel($programParticipationId, $id)
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot2::class);
        $task = new CancelBookedMentoringSlotTask($bookedMentoringSlotRepository, $id);
        $this->executeParticipantTask($programParticipationId, $task);

        return $this->show($programParticipationId, $id);
    }

    public function show($programParticipationId, $id)
    {
        $bookedMentoringSlot = $this->buildAndExecuteSingleQueryTask($programParticipationId, $id)->result;
        return $this->singleQueryResponse($this->arrayDataOfBookedMentoringSlot($bookedMentoringSlot));
    }
    
    public function submitReport($programParticipationId, $id)
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot2::class);
        $mentorRating = $this->integerOfInputRequest('mentorRating');

        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new ClientFileInfoFinder($fileInfoRepository, $this->firmId(), $this->clientId());        
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $payload = new SubmitMentoringReportPayload($id,
                $mentorRating, $formRecordData);
        $task = new SubmitBookedMentoringReportTask($bookedMentoringSlotRepository, $payload);
        $this->executeParticipantTask($programParticipationId, $task);
        
        return $this->show($programParticipationId, $id);
    }

    protected function buildAndExecuteSingleQueryTask($programParticipationId, $id): ShowBookedMentoringSlotTask
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot::class);
        $task = new ShowBookedMentoringSlotTask($bookedMentoringSlotRepository, $id);
        $this->executeQueryParticipantTask($programParticipationId, $task);
        return $task;
    }

    protected function arrayDataOfBookedMentoringSlot(BookedMentoringSlot $bookedMentoringSlot): array
    {
        return [
            'id' => $bookedMentoringSlot->getId(),
            'cancelled' => $bookedMentoringSlot->getCancelled(),
            'mentoringSlot' => [
                'id' => $bookedMentoringSlot->getMentoringSlot()->getId(),
                'cancelled' => $bookedMentoringSlot->getMentoringSlot()->getCancelled(),
                'capacity' => $bookedMentoringSlot->getMentoringSlot()->getCapacity(),
                'startTime' => $bookedMentoringSlot->getMentoringSlot()->getStartTimeString(),
                'endTime' => $bookedMentoringSlot->getMentoringSlot()->getEndTimeString(),
                'mediaType' => $bookedMentoringSlot->getMentoringSlot()->getMediaType(),
                'location' => $bookedMentoringSlot->getMentoringSlot()->getLocation(),
                'consultationSetup' => [
                    'id' => $bookedMentoringSlot->getMentoringSlot()->getConsultationSetup()->getId(),
                    'name' => $bookedMentoringSlot->getMentoringSlot()->getConsultationSetup()->getName(),
                    'participantFeedbackForm' => $this->arrayDataOfFeedbackForm($bookedMentoringSlot->getMentoringSlot()->getConsultationSetup()->getParticipantFeedbackForm()),
                ],
            ],
            'participantReport' => $this->arrayDataOfParticipantReport($bookedMentoringSlot->getParticipantReport()),
        ];
    }

    protected function arrayDataOfFeedbackForm(?FeedbackForm $feedbackForm): ?array
    {
        if (empty($feedbackForm)) {
            return null;
        }
        $data = (new FormToArrayDataConverter())->convert($feedbackForm);
        $data['id'] = $feedbackForm->getId();
        return $data;
    }

    protected function arrayDataOfParticipantReport(?ParticipantReport $participantReport): ?array
    {
        if (empty($participantReport)) {
            return null;
        }
        $data = (new FormRecordToArrayDataConverter())->convert($participantReport);
        $data['mentorRating'] = $participantReport->getMentorRating();
        $data['id'] = $participantReport->getId();
        return $data;
    }

}
