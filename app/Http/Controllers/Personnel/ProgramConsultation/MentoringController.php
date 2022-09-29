<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\Participant\DeclaredMentoring;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use Query\Domain\SharedModel\Mentoring\MentorReport;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\ExtendedMentoringFilter;
use Query\Domain\Task\InProgram\ViewAllMentoring;
use Query\Domain\Task\InProgram\ViewAllMentoringPayload;
use Query\Domain\Task\InProgram\ViewBookedMentoringSlotDetail;
use Query\Domain\Task\InProgram\ViewDeclaredMentoringDetail;
use Query\Domain\Task\InProgram\ViewMentoringRequestDetail;
use Query\Domain\Task\InProgram\ViewNegotiatedMentoringDetail;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMentoringRepository;

class MentoringController extends ConsultantBaseController
{

    public function viewAll($consultantId)
    {
        $mentoringRepository = new CustomDoctrineMentoringRepository($this->em);
        $task = new ViewAllMentoring($mentoringRepository);
        
        $filter = (new ExtendedMentoringFilter($this->getPage(), $this->getPageSize()))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setParticipantId($this->stripTagQueryRequest('participantId'))
                ->setOrderDirection($this->stripTagQueryRequest('order') ?? 'DESC');
        $payload = new ViewAllMentoringPayload($filter);
        
        $this->executeProgramQueryTask($consultantId, $task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    public function viewMentoringRequestDetail($consultantId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest::class);
        $task = new ViewMentoringRequestDetail($mentoringRequestRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($consultantId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfMentoringRequest($payload->result));
    }
    protected function arrayDataOfMentoringRequest(MentoringRequest $mentoringRequest): array
    {
        return [
            'id' => $mentoringRequest->getId(),
            'startTime' => $mentoringRequest->getStartTimeString(),
            'endTime' => $mentoringRequest->getEndTimeString(),
            'mediaType' => $mentoringRequest->getMediaType(),
            'location' => $mentoringRequest->getLocation(),
            'requestStatus' => $mentoringRequest->getRequestStatusString(),
            'mentor' => $this->arrayDataOfMentor($mentoringRequest->getMentor()),
            'consultationSetup' => [
                'id' => $mentoringRequest->getConsultationSetup()->getId(),
                'name' => $mentoringRequest->getConsultationSetup()->getName(),
            ],
        ];
    }
    
    public function viewNegotiatedMentoringDetail($consultantId, $id)
    {
        $negotiatedMentoringRepository = $this->em->getRepository(NegotiatedMentoring::class);
        $task = new ViewNegotiatedMentoringDetail($negotiatedMentoringRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($consultantId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfNegotiatedMentoring($payload->result));
    }
    protected function arrayDataOfNegotiatedMentoring(NegotiatedMentoring $negotiatedMentoring): array
    {
        $participantFeedbackform = $negotiatedMentoring->getMentoringRequest()->getConsultationSetup()->getParticipantFeedbackForm();
        $mentorFeedbackform = $negotiatedMentoring->getMentoringRequest()->getConsultationSetup()->getConsultantFeedbackForm();
        return [
            'id' => $negotiatedMentoring->getId(),
            'mentoringRequest' => [
                'id' => $negotiatedMentoring->getMentoringRequest()->getId(),
                'startTime' => $negotiatedMentoring->getMentoringRequest()->getStartTimeString(),
                'endTime' => $negotiatedMentoring->getMentoringRequest()->getEndTimeString(),
                'mediaType' => $negotiatedMentoring->getMentoringRequest()->getMediaType(),
                'location' => $negotiatedMentoring->getMentoringRequest()->getLocation(),
                'requestStatus' => $negotiatedMentoring->getMentoringRequest()->getRequestStatusString(),
                'mentor' => $this->arrayDataOfMentor($negotiatedMentoring->getMentoringRequest()->getMentor()),
                'consultationSetup' => $this->arrayDataOfConsultationSetup($negotiatedMentoring->getMentoringRequest()->getConsultationSetup()),
            ],
            'participantReport' => $this->arrayDataOfParticipantReport($negotiatedMentoring->getParticipantReport()),
            'mentorReport' => $this->arrayDataOfMentorReport($negotiatedMentoring->getMentorReport()),
        ];
    }
    protected function arrayDataOfFeedbackForm(?FeedbackForm $feedbackForm): ?array
    {
        if (empty($feedbackForm)) {
            return null;
        }
        return (new FormToArrayDataConverter())->convert($feedbackForm);
    }
    
    public function viewBookedMentoringSlotDetail($consultantId, $id)
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot::class);
        $task = new ViewBookedMentoringSlotDetail($bookedMentoringSlotRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($consultantId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfBookedMentoringSlot($payload->result));
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
                'mentor' => $this->arrayDataOfMentor($bookedMentoringSlot->getMentoringSlot()->getMentor()),
                'consultationSetup' => $this->arrayDataOfConsultationSetup($bookedMentoringSlot->getMentoringSlot()->getConsultationSetup()),
            ],
            'participantReport' => $this->arrayDataOfParticipantReport($bookedMentoringSlot->getParticipantReport()),
            'mentorReport' => $this->arrayDataOfMentorReport($bookedMentoringSlot->getMentorReport()),
        ];
    }
    
    public function viewDeclaredMentoringDetail($consultantId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring::class);
        $task = new ViewDeclaredMentoringDetail($declaredMentoringRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($consultantId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfDeclaredMentoring($payload->result));
    }
    protected function arrayDataOfDeclaredMentoring(DeclaredMentoring $declaredMentoring): array
    {
        return [
            'id' => $declaredMentoring->getId(),
            'startTime' => $declaredMentoring->getStartTimeString(),
            'endTime' => $declaredMentoring->getEndTimeString(),
            'mediaType' => $declaredMentoring->getMediaType(),
            'location' => $declaredMentoring->getLocation(),
            'declaredStatus' => $declaredMentoring->getDeclaredStatusDisplayValue(),
            'mentor' => $this->arrayDataOfMentor($declaredMentoring->getMentor()),
            'consultationSetup' => $this->arrayDataOfConsultationSetup($declaredMentoring->getConsultationSetup()),
            'participantReport' => $this->arrayDataOfParticipantReport($declaredMentoring->getMentoring()->getParticipantReport()),
            'mentorReport' => $this->arrayDataOfMentorReport($declaredMentoring->getMentoring()->getMentorReport()),
        ];
    }
    
    //
    protected function arrayDataOfConsultationSetup(ConsultationSetup $consultationSetup): ?array
    {
        return empty($consultationSetup) ? null : [
            'id' => $consultationSetup->getId(),
            'name' => $consultationSetup->getName(),
            'participantFeedbackForm' => $this->arrayDataOfFeedbackForm($consultationSetup->getParticipantFeedbackForm()),
            'mentorFeedbackForm' => $this->arrayDataOfFeedbackForm($consultationSetup->getConsultantFeedbackForm()),
        ];
    }
    protected function arrayDataOfParticipantReport(?ParticipantReport $participantReport): ?array
    {
        if (empty($participantReport)) {
            return null;
        }
        $participantReportData = (new FormRecordToArrayDataConverter())->convert($participantReport);
        $participantReportData['mentorRating'] = $participantReport->getMentorRating();
        return $participantReportData;
    }
    protected function arrayDataOfMentorReport(?MentorReport $mentorReport): ?array
    {
        if (empty($mentorReport)) {
            return null;
        }
        $mentorReportData = (new FormRecordToArrayDataConverter())->convert($mentorReport);
        $mentorReportData['participantRating'] = $mentorReport->getParticipantRating();
        return $mentorReportData;
    }
    protected function arrayDataOfMentor(Consultant $mentor): array
    {
        return [
            'id' => $mentor->getId(),
            'personnel' => [
                'id' => $mentor->getPersonnel()->getId(),
                'name' => $mentor->getPersonnel()->getName(),
            ],
        ];
    }

}
