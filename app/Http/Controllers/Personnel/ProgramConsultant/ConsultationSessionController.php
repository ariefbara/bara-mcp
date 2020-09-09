<?php

namespace App\Http\Controllers\Personnel\ProgramConsultant;

use App\Http\Controllers\ {
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    FormToArrayDataConverter,
    Personnel\PersonnelBaseController
};
use DateTimeImmutable;
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession\ConsultantFeedbackSet,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession,
    Domain\Service\PersonnelFileInfoFinder
};
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionView,
    Application\Service\Firm\Program\ConsulationSetup\ConsultationSessionFilter,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession as ConsultationSession2,
    Domain\Model\User\UserParticipant
};
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends PersonnelBaseController
{

    public function setConsultantFeedback($programConsultantId, $consultationSessionId)
    {
        $service = $this->buildSetConsultantFeedbackService();
        
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new PersonnelFileInfoFinder($fileInfoRepository, $this->firmId(), $this->personnelId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        
        $service->execute(
                $this->firmId(), $this->personnelId(), $programConsultantId, $consultationSessionId, $formRecordData);

        return $this->show($programConsultantId, $consultationSessionId);
    }

    public function show($programConsultantId, $consultationSessionId)
    {
        $service = $this->buildViewService();

        $consultationSession = $service->showById(
                $this->firmId(), $this->personnelId(), $programConsultantId, $consultationSessionId);

        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function showAll($programConsultantId)
    {
        $service = $this->buildViewService();
        
        $minStartTime = empty($minTime = $this->stripTagQueryRequest('minStartTime')) ? null : new DateTimeImmutable($minTime);
        $maxStartTime = empty($maxTime = $this->stripTagQueryRequest('maxStartTime')) ? null : new DateTimeImmutable($maxTime);
        $containParticipantFeedback = $this->filterBooleanOfInputRequest('containParticipantFeedback');
        $containConsultantFeedback = $this->filterBooleanOfInputRequest('containConsultantFeedback');
        
        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($minStartTime)
                ->setMaxStartTime($maxStartTime)
                ->setContainParticipantFeedback($containParticipantFeedback)
                ->setContainConsultantFeedback($containConsultantFeedback);
        
        $consultationSessions = $service->showAll(
                $this->firmId(), $this->personnelId(), $programConsultantId, $this->getPage(), $this->getPageSize(),
                $consultationSessionFilter);

        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTime(),
                "endTime" => $consultationSession->getEndTime(),
                "participant" => [
                    "id" => $consultationSession->getParticipant()->getId(),
                    "clientParticipant" => $this->arrayDataOfClientParticipant($consultationSession->getParticipant()->getClientParticipant()),
                    "userParticipant" => $this->arrayDataOfUserParticipant($consultationSession->getParticipant()->getUserParticipant()),
                ],
                "hasConsultantFeedback" => !empty($consultationSession->getConsultantFeedback()),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationSession(ConsultationSession2 $consultationSession): array
    {
        $consultantFeedbackData = empty($consultationSession->getConsultantFeedback()) ? null :
                (new FormRecordToArrayDataConverter())->convert($consultationSession->getConsultantFeedback());
        return [
            "id" => $consultationSession->getId(),
            "startTime" => $consultationSession->getStartTime(),
            "endTime" => $consultationSession->getEndTime(),
            "consultationSetup" => [
                "id" => $consultationSession->getConsultationSetup()->getId(),
                "name" => $consultationSession->getConsultationSetup()->getName(),
                "consultantFeedbackForm" => $this->arrayDataOfFeedbackForm(
                        $consultationSession->getConsultationSetup()->getConsultantFeedbackForm()),
            ],
            "participant" => [
                "id" => $consultationSession->getParticipant()->getId(),
                "clientParticipant" => $this->arrayDataOfClientParticipant($consultationSession->getParticipant()->getClientParticipant()),
                "userParticipant" => $this->arrayDataOfUserParticipant($consultationSession->getParticipant()->getUserParticipant()),
            ],
            "consultantFeedback" => $consultantFeedbackData,
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

    protected function arrayDataOfFeedbackForm(FeedbackForm $feedbackForm): array
    {
        $data = (new FormToArrayDataConverter())->convert($feedbackForm);
        $data['id'] = $feedbackForm->getId();
        return $data;
    }

    protected function buildSetConsultantFeedbackService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        return new ConsultantFeedbackSet($consultationSessionRepository);
    }

    protected function buildViewService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        return new ConsultationSessionView($consultationSessionRepository);
    }

}
