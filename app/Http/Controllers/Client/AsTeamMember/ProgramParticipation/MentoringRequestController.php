<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
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

class MentoringRequestController extends AsTeamMemberBaseController
{
    public function submitRequest($teamId, $teamProgramParticipationId)
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
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        
        $mentoringRequest = $this->buildAndExecuteSingleQueryTask($teamId, $teamProgramParticipationId, $task->requestedMentoringId);
        return $this->commandCreatedResponse($this->arrayDataOfMentoringRequest($mentoringRequest));
    }
    
    public function update($teamId, $teamProgramParticipationId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $mentoringRequestData = new MentoringRequestData($startTime, $mediaType, $location);
        $payload = new ChangeMentoringRequestPayload($id, $mentoringRequestData);
        
        $task = new ChangeMentoringRequestTask($mentoringRequestRepository, $payload);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        
        return $this->show($teamId, $teamProgramParticipationId, $id);
    }
    
    public function cancel($teamId, $teamProgramParticipationId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        $task = new CancelMentoringRequestTask($mentoringRequestRepository, $id);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        
        return $this->show($teamId, $teamProgramParticipationId, $id);
    }
    
    public function accept($teamId, $teamProgramParticipationId, $id)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        $task = new AcceptMentoringOfferingTask($mentoringRequestRepository, $id);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        
        return $this->show($teamId, $teamProgramParticipationId, $id);
    }
    
    public function show($teamId, $teamProgramParticipationId, $id)
    {
        $mentoringRequest = $this->buildAndExecuteSingleQueryTask($teamId, $teamProgramParticipationId, $id);
        return $this->singleQueryResponse($this->arrayDataOfMentoringRequest($mentoringRequest));
    }
    
    protected function buildAndExecuteSingleQueryTask($teamId, $teamProgramParticipationId, $id): MentoringRequest
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest::class);
        $task = new ShowMentoringRequestTask($mentoringRequestRepository, $id);
        $this->executeTeamParticipantQueryTask($teamId, $teamProgramParticipationId, $task);
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
