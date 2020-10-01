<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use DateTimeImmutable;
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestAccept,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestOffer,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestReject,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest
};
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestView,
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest as ConsultationRequest2,
    Domain\Model\User\UserParticipant
};
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends PersonnelBaseController
{

    public function accept($programConsultantId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $this->personnelId(), $programConsultantId, $consultationRequestId);

        return $this->show($programConsultantId, $consultationRequestId);
    }

    public function offer($programConsultantId, $consultationRequestId)
    {
        $service = $this->buildOfferService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $service->execute($this->firmId(), $this->personnelId(), $programConsultantId, $consultationRequestId, $startTime);

        return $this->show($programConsultantId, $consultationRequestId);
    }

    public function reject($programConsultantId, $consultationRequestId)
    {
        $service = $this->buildRejectService();
        $service->execute($this->firmId(), $this->personnelId(), $programConsultantId, $consultationRequestId);

        return $this->commandOkResponse();
    }

    public function show($programConsultantId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $consultationRequest = $service->showById(
                $this->firmId(), $this->personnelId(), $programConsultantId, $consultationRequestId);

        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($programConsultantId)
    {
        $service = $this->buildViewService();

        $minStartTime = empty($minTime = $this->stripTagQueryRequest('minStartTime')) ? null : new \DateTimeImmutable($minTime);
        $maxStartTime = empty($maxTime = $this->stripTagQueryRequest('maxStartTime')) ? null : new \DateTimeImmutable($maxTime);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($minStartTime)
                ->setMaxStartTime($maxStartTime);

        $consultationRequests = $service->showAll(
                $this->firmId(), $this->personnelId(), $programConsultantId, $this->getPage(), $this->getPageSize(),
                $consultationRequestFilter);

        $result = [];
        $result['total'] = count($consultationRequests);
        foreach ($consultationRequests as $consultationRequest) {
            $result['list'][] = $this->arrayDataOfConsultationRequest($consultationRequest);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationRequest(ConsultationRequest2 $consultationRequest): array
    {
        return [
            "id" => $consultationRequest->getId(),
            "startTime" => $consultationRequest->getStartTimeString(),
            "endTime" => $consultationRequest->getEndTimeString(),
            "concluded" => $consultationRequest->isConcluded(),
            "status" => $consultationRequest->getStatus(),
            "consultationSetup" => [
                "id" => $consultationRequest->getConsultationSetup()->getId(),
                "name" => $consultationRequest->getConsultationSetup()->getName(),
            ],
            "participant" => [
                "id" => $consultationRequest->getParticipant()->getId(),
                "clientParticipant" => $this->arrayDataOfClientParticipant($consultationRequest->getParticipant()->getClientParticipant()),
                "userParticipant" => $this->arrayDataOfUserParticipant($consultationRequest->getParticipant()->getUserParticipant()),
            ],
        ];
    }
    
    protected function arrayDataOfClientParticipant(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant)? null: [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfUserParticipant(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant)? null: [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }

    protected function buildAcceptService()
    {
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);
        $dispatcher = new Dispatcher();

        return new ConsultationRequestAccept($programConsultantRepository, $dispatcher);
    }

    protected function buildOfferService()
    {
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);
        $dispatcher = new Dispatcher();

        return new ConsultationRequestOffer($programConsultantRepository, $dispatcher);
    }

    protected function buildRejectService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        return new ConsultationRequestReject($consultationRequestRepository);
    }

    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        return new ConsultationRequestView($consultationRequestRepository);
    }

}
