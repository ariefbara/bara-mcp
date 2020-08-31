<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\ {
    Client\ClientBaseController,
    SwiftMailerBuilder
};
use Config\EventList;
use DateTimeImmutable;
use Firm\ {
    Application\Listener\Firm\Program\ConsultationSetup\ClientAcceptedConsultationRequestListener,
    Application\Listener\Firm\Program\ConsultationSetup\ClientUpdatedConsultationRequestListener,
    Application\Service\Firm\Program\ConsultationSetup\SendClientConsultationRequestMail,
    Application\Service\Firm\Program\ConsultationSetup\SendClientConsultationSessionMail,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest as ConsultationRequest3,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession
};
use Participant\ {
    Application\Service\Participant\ClientAcceptConsultationRequest,
    Application\Service\Participant\ClientCancelConcultationRequest,
    Application\Service\Participant\ClientChangeConsultationRequestTime,
    Application\Service\Participant\ClientSubmitConsultationRequest,
    Domain\Model\ClientParticipant,
    Domain\Model\DependencyEntity\Firm\Program\Consultant,
    Domain\Model\Participant\ConsultationRequest as ConsultationRequest2
};
use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ViewConsultationRequest,
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
};
use Resources\Application\Event\Dispatcher;
use SharedContext\Domain\Model\Firm\Program\ConsultationSetup;

class ConsultationRequestController extends ClientBaseController
{

    public function propose($programId)
    {
        $service = $this->buildProposeService();

        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $personnelId = $this->stripTagsInputRequest('personnelId');
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));

        $consultationRequestId = $service->execute(
                $this->firmId(), $this->clientId(), $programId, $consultationSetupId, $personnelId, $startTime);

        $viewService = $this->buildViewService();
        $consultationRequest = $viewService
                ->showById($this->firmId(), $this->clientId(), $programId, $consultationRequestId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function cancel($programId, $consultationRequestId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $programId, $consultationRequestId);
        return $this->commandOkResponse();
    }

    public function rePropose($programId, $consultationRequestId)
    {
        $service = $this->buildReproposeService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $service->execute($this->firmId(), $this->clientId(), $programId, $consultationRequestId, $startTime);

        return $this->show($programId, $consultationRequestId);
    }

    public function accept($programId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $this->clientId(), $programId, $consultationRequestId);

        return $this->show($programId, $consultationRequestId);
    }

    public function show($programId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $consultationRequest = $service->showById($this->firmId(), $this->clientId(), $programId, $consultationRequestId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();

        $minStartTime = empty($minTime = $this->stripTagQueryRequest('minStartTime')) ? null : new DateTimeImmutable($minTime);
        $maxStartTime = empty($maxTime = $this->stripTagQueryRequest('maxStartTime')) ? null : new DateTimeImmutable($maxTime);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($minStartTime)
                ->setMaxStartTime($maxStartTime);

        $consultationRequests = $service->showAll(
                $this->firmId(), $this->clientId(), $programId, $this->getPage(), $this->getPageSize(),
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

        $dispatcher->addListener(
                EventList::CLIENT_PROPOSED_CONSULTATION_REQUEST, $this->buildClientUpdatedConsultationRequestListener());

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

        $dispatcher->addListener(
                EventList::CLIENT_ACCEPTED_CONSULTATION_REQUEST, $this->buildClientAcceptedConsultationRequestListener());

        return new ClientAcceptConsultationRequest($clientParticipantRepository, $dispatcher);
    }

    protected function buildReproposeService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $dispatcher = new Dispatcher();

        $dispatcher->addListener(
                EventList::CLIENT_CHANGED_CONSULTATION_REQUEST_TIME,
                $this->buildClientUpdatedConsultationRequestListener());

        return new ClientChangeConsultationRequestTime($clientParticipantRepository, $dispatcher);
    }

    protected function buildClientUpdatedConsultationRequestListener()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest3::class);
        $mailer = SwiftMailerBuilder::build();

        $sendClientConsultationRequestMail = new SendClientConsultationRequestMail($consultationRequestRepository,
                $mailer);
        return new ClientUpdatedConsultationRequestListener($sendClientConsultationRequestMail);
    }

    protected function buildClientAcceptedConsultationRequestListener()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        $mailer = SwiftMailerBuilder::build();

        $sendClientConsultationSessionMail = new SendClientConsultationSessionMail($consultationSessionRepository,
                $mailer);
        return new ClientAcceptedConsultationRequestListener($sendClientConsultationSessionMail);
    }

}
