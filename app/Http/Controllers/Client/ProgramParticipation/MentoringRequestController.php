<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant\MentoringRequest as MentoringRequest2;
use Participant\Domain\Model\Participant\MentoringRequestData;
use Participant\Domain\Task\Participant\AcceptMentoringOfferingTask;
use Participant\Domain\Task\Participant\CancelMentoringRequestTask;
use Participant\Domain\Task\Participant\ChangeMentoringRequestPayload;
use Participant\Domain\Task\Participant\ChangeMentoringRequestTask;
use Participant\Domain\Task\Participant\RequestMentoringPayload;
use Participant\Domain\Task\Participant\RequestMentoringTask;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Task\Participant\ShowMentoringRequestTask;

class MentoringRequestController extends ClientParticipantBaseController
{
    public function submitRequest($programParticipationId)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        $mentorRepository = $this->em->getRepository(Consultant::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        
        $mentorId = $this->stripTagsInputRequest('mentorId');
        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $mentoringRequestData = new MentoringRequestData($startTime, $mediaType, $location);
        $payload = new RequestMentoringPayload($mentorId, $consultationSetupId, $mentoringRequestData);
        
        $task = new RequestMentoringTask(
                $mentoringRequestRepository, $mentorRepository, $consultationSetupRepository, $payload);
        $this->executeParticipantTask($programParticipationId, $task);
        
        $mentoringRequest = $this->buildAndExecuteSingleQueryTask($programParticipationId, $task->requestedMentoringId);
        return $this->commandCreatedResponse($this->arrayDataOfMentoringRequest($mentoringRequest));
    }
    
    public function update($programParticipationId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $mentoringRequestData = new MentoringRequestData($startTime, $mediaType, $location);
        $payload = new ChangeMentoringRequestPayload($id, $mentoringRequestData);
        
        $task = new ChangeMentoringRequestTask($mentoringRequestRepository, $payload);
        $this->executeParticipantTask($programParticipationId, $task);
        
        return $this->show($programParticipationId, $id);
    }
    
    public function cancel($programParticipationId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        $task = new CancelMentoringRequestTask($mentoringRequestRepository, $id);
        $this->executeParticipantTask($programParticipationId, $task);
        
        return $this->show($programParticipationId, $id);
    }
    
    public function accept($programParticipationId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        $task = new AcceptMentoringOfferingTask($mentoringRequestRepository, $id);
        $this->executeParticipantTask($programParticipationId, $task);
        
        return $this->show($programParticipationId, $id);
    }
    
    public function show($programParticipationId, $id)
    {
        $mentoringRequest = $this->buildAndExecuteSingleQueryTask($programParticipationId, $id);
        return $this->singleQueryResponse($this->arrayDataOfMentoringRequest($mentoringRequest));
    }
    
    protected function buildAndExecuteSingleQueryTask($programParticipationId, $id): MentoringRequest
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest::class);
        $task = new ShowMentoringRequestTask($mentoringRequestRepository, $id);
        $this->executeQueryParticipantTask($programParticipationId, $task);
        return $task->result;
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
            'mentor' => [
                'id' => $mentoringRequest->getMentor()->getId(),
                'personnel' => [
                    'id' => $mentoringRequest->getMentor()->getPersonnel()->getId(),
                    'name' => $mentoringRequest->getMentor()->getPersonnel()->getName(),
                ],
            ],
            'consultationSetup' => [
                'id' => $mentoringRequest->getConsultationSetup()->getId(),
                'name' => $mentoringRequest->getConsultationSetup()->getName(),
            ],
        ];
    }
}
