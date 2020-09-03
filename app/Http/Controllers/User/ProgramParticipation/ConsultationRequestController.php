<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\User\UserBaseController;
use DateTimeImmutable;
use Participant\ {
    Application\Service\UserParticipant\UserParticipantAcceptConsultationRequest,
    Application\Service\UserParticipant\UserParticipantCancelConcultationRequest,
    Application\Service\UserParticipant\UserParticipantChangeConsultationRequestTime,
    Application\Service\UserParticipant\UserParticipantSubmitConsultationRequest,
    Domain\Model\DependencyEntity\Firm\Program\Consultant,
    Domain\Model\DependencyEntity\Firm\Program\ConsultationSetup,
    Domain\Model\UserParticipant
};
use Query\ {
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Application\Service\User\ProgramParticipation\ViewConsultationRequest,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
};
use Resources\Application\Event\Dispatcher;
use \Participant\Domain\Model\Participant\ConsultationRequest as ConsultationRequest2;

class ConsultationRequestController extends UserBaseController
{

    public function propose($programParticipationId)
    {
        $service = $this->buildProposeService();

        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $consultantId = $this->stripTagsInputRequest('consultantId');
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));

        $consultationRequestId = $service->execute(
                $this->userId(), $programParticipationId, $consultationSetupId, $consultantId, $startTime);

        $viewService = $this->buildViewService();
        $consultationRequest = $viewService
                ->showById($this->userId(), $programParticipationId, $consultationRequestId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function cancel($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->userId(), $programParticipationId, $consultationRequestId);
        return $this->commandOkResponse();
    }

    public function rePropose($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildReproposeService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $service->execute($this->userId(), $programParticipationId, $consultationRequestId, $startTime);

        return $this->show($programParticipationId, $consultationRequestId);
    }

    public function accept($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->userId(), $programParticipationId, $consultationRequestId);

        return $this->show($programParticipationId, $consultationRequestId);
    }

    public function show($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $consultationRequest = $service->showById($this->userId(), $programParticipationId, $consultationRequestId);
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
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize(),
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
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $dispatcher = new Dispatcher();

//        $dispatcher->addListener(
//                EventList::CLIENT_PARTICIPANT_PROPOSED_CONSULTATION_REQUEST, $this->buildUserUpdatedConsultationRequestListener());

        return new UserParticipantSubmitConsultationRequest(
                $consultationRequestRepository, $userParticipantRepository, $consultationSetupRepository,
                $consultantRepository, $dispatcher);
    }

    protected function buildCancelService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        return new UserParticipantCancelConcultationRequest($consultationRequestRepository);
    }

    protected function buildAcceptService()
    {

        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $dispatcher = new Dispatcher();

//        $dispatcher->addListener(
//                EventList::CLIENT_PARTICIPANT_ACCEPTED_CONSULTATION_REQUEST, $this->buildUserAcceptedConsultationRequestListener());

        return new UserParticipantAcceptConsultationRequest($userParticipantRepository, $dispatcher);
    }

    protected function buildReproposeService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $dispatcher = new Dispatcher();

//        $dispatcher->addListener(
//                EventList::CLIENT_PARTICIPANT_CHANGED_CONSULTATION_REQUEST_TIME,
//                $this->buildUserUpdatedConsultationRequestListener());

        return new UserParticipantChangeConsultationRequestTime($userParticipantRepository, $dispatcher);
    }

}
