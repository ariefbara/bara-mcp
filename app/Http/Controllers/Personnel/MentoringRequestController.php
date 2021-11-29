<?php

namespace App\Http\Controllers\Personnel;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest as MentoringRequest2;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequestData;
use Personnel\Domain\Task\Mentor\ApproveMentoringRequestTask;
use Personnel\Domain\Task\Mentor\OfferMentoringRequestPayload;
use Personnel\Domain\Task\Mentor\OfferMentoringRequestTask;
use Personnel\Domain\Task\Mentor\RejectMentoringRequestTask;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Personnel\ShowMentoringRequestTask;

class MentoringRequestController extends PersonnelBaseController
{
    public function reject($mentorId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        $task = new RejectMentoringRequestTask($mentoringRequestRepository, $id);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        
        return $this->show($id);
    }
    
    public function approve($mentorId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        $task = new ApproveMentoringRequestTask($mentoringRequestRepository, $id);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        
        return $this->show($id);
    }
    
    public function offer($mentorId, $id)
    {
        
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $mentoringRequestData = new MentoringRequestData($startTime, $mediaType, $location);
        $payload = new OfferMentoringRequestPayload($id, $mentoringRequestData);
        
        $task = new OfferMentoringRequestTask($mentoringRequestRepository, $payload);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        
        return $this->show($id);
    }
    
    public function show($id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest::class);
        $task = new ShowMentoringRequestTask($mentoringRequestRepository, $id);
        $this->executePersonnelQueryTask($task);
        
        return $this->singleQueryResponse($this->arrayDataOfMentoringRequest($task->result));
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
            'participant' => [
                'id' => $mentoringRequest->getParticipant()->getId(),
                'client' => $this->arrayDataOfClient($mentoringRequest->getParticipant()->getClientParticipant()),
                'team' => $this->arrayDataOfClient($mentoringRequest->getParticipant()->getTeamParticipant()),
                'user' => $this->arrayDataOfClient($mentoringRequest->getParticipant()->getUserParticipant()),
            ],
            'consultationSetup' => [
                'id' => $mentoringRequest->getConsultationSetup()->getId(),
                'name' => $mentoringRequest->getConsultationSetup()->getName(),
            ],
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
