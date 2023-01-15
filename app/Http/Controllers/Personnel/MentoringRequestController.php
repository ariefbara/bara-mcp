<?php

namespace App\Http\Controllers\Personnel;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest as MentoringRequest2;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequestData;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Mentor\ApproveMentoringRequestTask;
use Personnel\Domain\Task\Mentor\OfferMentoringRequestPayload;
use Personnel\Domain\Task\Mentor\OfferMentoringRequestTask;
use Personnel\Domain\Task\Mentor\RejectMentoringRequestTask;
use Personnel\Domain\Task\Mentor\RequestMentoring;
use Personnel\Domain\Task\Mentor\RequestMentoringPayload;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestSearch;
use Query\Domain\Task\Personnel\ShowMentoringRequestTask;
use Query\Domain\Task\Personnel\ViewAllMentoringRequest;
use Query\Domain\Task\Personnel\ViewAllMentoringRequestPayload;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;

class MentoringRequestController extends PersonnelBaseController
{
    
    protected function getMentoringRequestData() {
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        return new MentoringRequestData($startTime, $mediaType, $location);
    }
    
    protected function queryMentoringRequestDetail($id) {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest::class);
        $task = new ShowMentoringRequestTask($mentoringRequestRepository, $id);
        $this->executePersonnelQueryTask($task);
        return $this->arrayDataOfMentoringRequest($task->result);
    }

    public function propose($mentorId)
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest2::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new RequestMentoring($mentoringRequestRepository, $consultationSetupRepository, $participantRepository);
        
        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $participantId = $this->stripTagsInputRequest('participantId');
        $payload = new RequestMentoringPayload($this->getMentoringRequestData(), $consultationSetupId, $participantId);
        
        $this->executeExtendedMentorTaskInPersonnelContext($mentorId, $task, $payload);
        
        return $this->commandCreatedResponse($this->queryMentoringRequestDetail($payload->requestedMentoringId));
    }
    
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

    public function showAllUnresponded()
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest::class);
        $mentoringRequestSearch = (new MentoringRequestSearch())
                ->addRequestStatus(MentoringRequestStatus::REQUESTED)
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        $viewAllMentoringRequestPayload = new ViewAllMentoringRequestPayload(
                $this->getPage(), $this->getPageSize(), $mentoringRequestSearch);
        $task = new ViewAllMentoringRequest($mentoringRequestRepository, $viewAllMentoringRequestPayload);
        
        $this->executePersonnelQueryTask($task);
        
        $result  = [];
        $result['total'] = count($viewAllMentoringRequestPayload->result);
        foreach ($viewAllMentoringRequestPayload->result as $mentoringRequest) {
            $result['list'][] = $this->arrayDataOfMentoringRequest($mentoringRequest);
        }
        return $this->listQueryResponse($result);
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
                'team' => $this->arrayDataOfTeam($mentoringRequest->getParticipant()->getTeamParticipant()),
                'user' => $this->arrayDataOfUser($mentoringRequest->getParticipant()->getUserParticipant()),
            ],
            'consultationSetup' => [
                'id' => $mentoringRequest->getConsultationSetup()->getId(),
                'name' => $mentoringRequest->getConsultationSetup()->getName(),
            ],
            'mentor' => [
                'id' => $mentoringRequest->getMentor()->getId(),
                'program' => [
                    'id' => $mentoringRequest->getMentor()->getProgram()->getId(),
                    'name' => $mentoringRequest->getMentor()->getProgram()->getName(),
                ],
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
