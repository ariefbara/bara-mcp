<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\DeclaredMentoring;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\SharedModel\Mentoring\MentorReport;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\ExtendedMentoringFilter;
use Query\Domain\Task\InProgram\ViewAllMentoring;
use Query\Domain\Task\InProgram\ViewAllMentoringPayload;
use Query\Domain\Task\InProgram\ViewBookedMentoringSlotDetail;
use Query\Domain\Task\InProgram\ViewDeclaredMentoringDetail;
use Query\Domain\Task\InProgram\ViewMentoringRequestDetail;
use Query\Domain\Task\InProgram\ViewMentoringSlotDetail;
use Query\Domain\Task\InProgram\ViewNegotiatedMentoringDetail;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMentoringRepository;

class MentoringController extends CoordinatorBaseController
{

    public function viewAll($coordinatorId)
    {
        $mentoringRepository = new CustomDoctrineMentoringRepository($this->em);
        $task = new ViewAllMentoring($mentoringRepository);
        
        $filter = (new ExtendedMentoringFilter($this->getPage(), $this->getPageSize()))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setParticipantId($this->stripTagQueryRequest('participantId'))
                ->setOrderDirection($this->stripTagQueryRequest('order') ?? 'DESC');
        $payload = new ViewAllMentoringPayload($filter);
        
        $this->executeProgramQueryTask($coordinatorId, $task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    public function viewMentoringRequestDetail($coordinatorId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest::class);
        $task = new ViewMentoringRequestDetail($mentoringRequestRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($coordinatorId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfMentoringRequest($payload->result));
    }
    protected function arrayDataOfMentoringRequest(MentoringRequest $mentoringRequest): array
    {
        $negotiatedMentoring = $mentoringRequest->getNegotiatedMentoring();
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
            'participantReport' => $negotiatedMentoring ? $this->arrayDataOfParticipantReport($negotiatedMentoring->getParticipantReport()) : null,
            'mentorReport' => $negotiatedMentoring ? $this->arrayDataOfMentorReport($negotiatedMentoring->getMentorReport()) : null,
        ];
    }
    
    public function viewNegotiatedMentoringDetail($coordinatorId, $id)
    {
        $negotiatedMentoringRepository = $this->em->getRepository(NegotiatedMentoring::class);
        $task = new ViewNegotiatedMentoringDetail($negotiatedMentoringRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($coordinatorId, $task, $payload);
        
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
    
    public function viewBookedMentoringSlotDetail($coordinatorId, $id)
    {
        $bookedMentoringSlotRepository = $this->em->getRepository(BookedMentoringSlot::class);
        $task = new ViewBookedMentoringSlotDetail($bookedMentoringSlotRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($coordinatorId, $task, $payload);
        
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
    
    public function viewDeclaredMentoringDetail($coordinatorId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring::class);
        $task = new ViewDeclaredMentoringDetail($declaredMentoringRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($coordinatorId, $task, $payload);
        
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
    
    public function viewMentoringSlotDetail($coordinatorId, $id)
    {
        $mentoringSlotRepository = $this->em->getRepository(MentoringSlot::class);
        $task = new ViewMentoringSlotDetail($mentoringSlotRepository);
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeProgramQueryTask($coordinatorId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfMentoringSlot($payload->result));
    }
    protected function arrayDataOfMentoringSlot(MentoringSlot $mentoringSlot): array
    {
        $bookedSlots = [];
        foreach ($mentoringSlot->iterateActiveBookedSlots() as $bookedMentoringSlot) {
            $bookedSlots[] = [
                'id' => $bookedMentoringSlot->getId(),
                'participant' => $this->arrayDataOfParticipant($bookedMentoringSlot->getParticipant()),
                'participantReport' => $this->arrayDataOfParticipantReport($bookedMentoringSlot->getParticipantReport()),
                'mentorReport' => $this->arrayDataOfMentorReport($bookedMentoringSlot->getMentorReport()),
            ];
        }
        return [
            'id' => $mentoringSlot->getId(),
            'cancelled' => $mentoringSlot->getCancelled(),
            'capacity' => $mentoringSlot->getCapacity(),
            'startTime' => $mentoringSlot->getStartTimeString(),
            'endTime' => $mentoringSlot->getEndTimeString(),
            'mediaType' => $mentoringSlot->getMediaType(),
            'location' => $mentoringSlot->getLocation(),
            'mentor' => $this->arrayDataOfMentor($mentoringSlot->getMentor()),
            'consultationSetup' => $this->arrayDataOfConsultationSetup($mentoringSlot->getConsultationSetup()),
            'bookedSlots' => $bookedSlots,
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
    
    //
    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            'id' => $participant->getId(),
            'client' => $this->arrayDataOfClient($participant->getClientParticipant()),
            'team' => $this->arrayDataOfTeam($participant->getTeamParticipant()),
            'user' => $this->arrayDataOfUser($participant->getUserParticipant()),
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }

}
