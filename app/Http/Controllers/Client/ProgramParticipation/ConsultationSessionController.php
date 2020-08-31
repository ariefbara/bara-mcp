<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\ {
    Client\ClientBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    FormToArrayDataConverter
};
use DateTimeImmutable;
use Participant\ {
    Application\Service\Participant\ConsultationSession\ParticipantFeedbackSet,
    Domain\Model\Participant\ConsultationSession as ConsultationSession2,
    Domain\Service\ClientFileInfoFinder
};
use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ViewConsultationSession,
    Application\Service\Firm\Program\ConsulationSetup\ConsultationSessionFilter,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession
};
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends ClientBaseController
{

    public function setParticipantFeedback($programId, $consultationSessionId)
    {
        $service = $this->buildSetParticipantFeedbackService();
        $formRecordData = $this->getFormRecordData();
        $service->execute($this->firmId(), $this->clientId(), $programId, $consultationSessionId, $formRecordData);
        
        return $this->show($programId, $consultationSessionId);
    }

    public function show($programId, $consultationSessionId)
    {
        $service = $this->buildViewService();
        $consultationSession = $service->showById($this->firmId(), $this->clientId(), $programId, $consultationSessionId);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        
        $minStartTime = empty($minTime = $this->stripTagQueryRequest('minStartTime')) ? null : new DateTimeImmutable($minTime);
        $maxStartTime = empty($maxTime = $this->stripTagQueryRequest('maxStartTime')) ? null : new DateTimeImmutable($maxTime);
        $containParticipantFeedback = $this->stripTagQueryRequest('containParticipantFeedback');
        $containConsultantFeedback = $this->stripTagQueryRequest('containConsultantFeedback');

        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($minStartTime)
                ->setMaxStartTime($maxStartTime)
                ->setContainParticipantFeedback($containParticipantFeedback)
                ->setContainConsultantFeedback($containConsultantFeedback);
        
        $consultationSessions = $service->showAll($this->firmId(), $this->clientId(), $programId, $this->getPage(), $this->getPageSize(), $consultationSessionFilter);
        
        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTime(),
                "endTime" => $consultationSession->getEndTime(),
                "consultationSetup" => [
                    "id" => $consultationSession->getConsultationSetup()->getId(),
                    "name" => $consultationSession->getConsultationSetup()->getName()
                ],
                "consultant" => [
                    "id" => $consultationSession->getConsultant()->getId(),
                    "personnel" => [
                        "id" => $consultationSession->getConsultant()->getPersonnel()->getId(),
                        "name" => $consultationSession->getConsultant()->getPersonnel()->getName()
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationSession(ConsultationSession $consultationSession)
    {
        $participantFeedback = empty($consultationSession->getParticipantFeedback()) ? null :
                (new FormRecordToArrayDataConverter())->convert($consultationSession->getParticipantFeedback());
        return [
            "id" => $consultationSession->getId(),
            "startTime" => $consultationSession->getStartTime(),
            "endTime" => $consultationSession->getEndTime(),
            "consultationSetup" => [
                "id" => $consultationSession->getConsultationSetup()->getId(),
                "name" => $consultationSession->getConsultationSetup()->getName(),
                "participantFeedbackForm" => $this->arrayDataOfFeedbackForm(
                        $consultationSession->getConsultationSetup()->getParticipantFeedbackForm()),
            ],
            "consultant" => [
                "id" => $consultationSession->getConsultant()->getId(),
                "personnel" => [
                    "id" => $consultationSession->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationSession->getConsultant()->getPersonnel()->getName()
                ],
            ],
            "participantFeedback" => $participantFeedback,
        ];
    }

    protected function arrayDataOfFeedbackForm(FeedbackForm $feedbackForm): array
    {
        $data = (new FormToArrayDataConverter())->convert($feedbackForm);
        $data['id'] = $feedbackForm->getId();
        return $data;
    }

    protected function buildViewService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        return new ViewConsultationSession($consultationSessionRepository);
    }

    protected function buildSetParticipantFeedbackService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        return new ParticipantFeedbackSet($consultationSessionRepository);
    }
    
    protected function getFormRecordData()
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new ClientFileInfoFinder($fileInfoRepository, $this->firmId(), $this->clientId());
        return (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
    }

}
