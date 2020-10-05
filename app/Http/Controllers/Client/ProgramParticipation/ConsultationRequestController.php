<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use DateTimeImmutable;
use Participant\ {
    Application\Service\ClientParticipant\ClientAcceptConsultationRequest,
    Application\Service\ClientParticipant\ClientCancelConcultationRequest,
    Application\Service\ClientParticipant\ClientChangeConsultationRequestTime,
    Application\Service\ClientParticipant\ClientSubmitConsultationRequest,
    Domain\DependencyModel\Firm\Program\Consultant,
    Domain\DependencyModel\Firm\Program\ConsultationSetup,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\ConsultationRequest as ConsultationRequest2
};
use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ViewConsultationRequest,
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
};
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends ClientBaseController
{

    public function propose($programParticipationId)
    {
        $service = $this->buildProposeService();

        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $consultantId = $this->stripTagsInputRequest('consultantId');
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));

        $consultationRequestId = $service->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $consultationSetupId, $consultantId,
                $startTime);

        $viewService = $this->buildViewService();
        $consultationRequest = $viewService
                ->showById($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function cancel($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        return $this->commandOkResponse();
    }

    public function rePropose($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildReproposeService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId,
                $startTime);

        return $this->show($programParticipationId, $consultationRequestId);
    }

    public function accept($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);

        return $this->show($programParticipationId, $consultationRequestId);
    }

    public function show($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $consultationRequest = $service->showById($this->firmId(), $this->clientId(), $programParticipationId,
                $consultationRequestId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();

        $minStartTime = empty($minTime = $this->stripTagQueryRequest('minStartTime')) ? null : new DateTimeImmutable($minTime);
        $maxStartTime = empty($maxTime = $this->stripTagQueryRequest('maxStartTime')) ? null : new DateTimeImmutable($maxTime);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($minStartTime)
                ->setMaxStartTime($maxStartTime);

        $consultationRequests = $service->showAll(
                $this->firmId(), $this->clientId(), $programParticipationId, $this->getPage(), $this->getPageSize(),
                $consultationRequestFilter);

        $result = [];
        $result['total'] = count($consultationRequests);
        foreach ($consultationRequests as $consultationRequest) {
            $result['list'][] = $this->arrayDataOfConsultationRequest($consultationRequest);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationRequest(ConsultationRequest $consultationRequest): array
    {
        return [
            "id" => $consultationRequest->getId(),
            "consultationSetup" => [
                "id" => $consultationRequest->getConsultationSetup()->getId(),
                "name" => $consultationRequest->getConsultationSetup()->getName(),
            ],
            "consultant" => [
                "id" => $consultationRequest->getConsultant()->getId(),
                "personnel" => [
                    "id" => $consultationRequest->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationRequest->getConsultant()->getPersonnel()->getName(),
                ],
            ],
            "startTime" => $consultationRequest->getStartTimeString(),
            "endTime" => $consultationRequest->getEndTimeString(),
            "concluded" => $consultationRequest->isConcluded(),
            "status" => $consultationRequest->getStatus(),
        ];
    }

    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        return new ViewConsultationRequest($consultationRequestRepository);
    }

    protected function buildProposeService()
    {

        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $dispatcher = new Dispatcher();

//        $dispatcher->addListener(
//                EventList::CLIENT_PARTICIPANT_PROPOSED_CONSULTATION_REQUEST, $this->buildClientUpdatedConsultationRequestListener());

        return new ClientSubmitConsultationRequest(
                $consultationRequestRepository, $clientParticipantRepository, $consultationSetupRepository,
                $consultantRepository, $dispatcher);
    }

    protected function buildCancelService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        return new ClientCancelConcultationRequest($consultationRequestRepository);
    }

    protected function buildAcceptService()
    {

        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $dispatcher = new Dispatcher();

//        $dispatcher->addListener(
//                EventList::CLIENT_PARTICIPANT_ACCEPTED_CONSULTATION_REQUEST, $this->buildClientAcceptedConsultationRequestListener());

        return new ClientAcceptConsultationRequest($clientParticipantRepository, $dispatcher);
    }

    protected function buildReproposeService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $dispatcher = new Dispatcher();

//        $dispatcher->addListener(
//                EventList::CLIENT_PARTICIPANT_CHANGED_CONSULTATION_REQUEST_TIME,
//                $this->buildClientUpdatedConsultationRequestListener());

        return new ClientChangeConsultationRequestTime($clientParticipantRepository, $dispatcher);
    }

}
