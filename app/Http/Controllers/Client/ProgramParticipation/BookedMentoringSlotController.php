<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\Model\Participant\BookedMentoringSlot as BookedMentoringSlot2;
use Participant\Domain\Task\Participant\BookMentoringSlotPayload;
use Participant\Domain\Task\Participant\BookMentoringSlotTask;
use Participant\Domain\Task\Participant\CancelBookedMentoringSlotTask;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;
use Query\Domain\Task\Participant\ShowBookedMentoringSlotTask;

class BookedMentoringSlotController extends ClientParticipantBaseController
{

    public function book($clientParticipantId)
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot2::class);
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot::class);
        $mentoringSlotId = $this->stripTagsInputRequest('mentoringSlotId');
        $payload = new BookMentoringSlotPayload($mentoringSlotId);

        $task = new BookMentoringSlotTask($bookedMentoringSlotRepository, $mentoringSlotRepository, $payload);
        $this->executeParticipantTask($clientParticipantId, $task);

        $bookedMentoringSlot = $this->buildAndExecuteSingleQueryTask(
                        $clientParticipantId, $task->bookedMentoringSlotId)->result;
        return $this->commandCreatedResponse($this->arrayDataOfBookedMentoringSlot($bookedMentoringSlot));
    }

    public function cancel($clientParticipantId, $id)
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot2::class);
        $task = new CancelBookedMentoringSlotTask($bookedMentoringSlotRepository, $id);
        $this->executeParticipantTask($clientParticipantId, $task);

        return $this->show($clientParticipantId, $id);
    }

    public function show($clientParticipantId, $id)
    {
        $bookedMentoringSlot = $this->buildAndExecuteSingleQueryTask($clientParticipantId, $id)->result;
        return $this->singleQueryResponse($this->arrayDataOfBookedMentoringSlot($bookedMentoringSlot));
    }

    protected function buildAndExecuteSingleQueryTask($clientParticipantId, $id): ShowBookedMentoringSlotTask
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot::class);
        $task = new ShowBookedMentoringSlotTask($bookedMentoringSlotRepository, $id);
        $this->executeQueryParticipantTask($clientParticipantId, $task);
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
        $data['name'] = $feedbackForm->getName();
        return $data;
    }

    protected function arrayDataOfParticipantReport(?ParticipantReport $participantReport): ?array
    {
        if (empty($participantReport)) {
            return null;
        }
        $data = (new FormRecordToArrayDataConverter())->convert($participantReport);
        $data['id'] = $participantReport->getId();
        $data['mentorRating'] = $participantReport->getMentorRating();
        return $data;
    }

}
