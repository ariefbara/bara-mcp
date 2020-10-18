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
    Application\Service\ClientParticipant\ConsultationSession\ParticipantFeedbackSet,
    Domain\Model\Participant\ConsultationSession as ConsultationSession2,
    Domain\Service\ClientFileInfoFinder
};
use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ViewConsultationSession,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends ClientBaseController
{

    public function submitReport($programParticipationId, $consultationSessionId)
    {
        $service = $this->buildSetParticipantFeedbackService();
        $formRecordData = $this->getFormRecordData();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationSessionId, $formRecordData);
        
        return $this->show($programParticipationId, $consultationSessionId);
    }

    public function show($programParticipationId, $consultationSessionId)
    {
        $service = $this->buildViewService();
        $consultationSession = $service->showById($this->firmId(), $this->clientId(), $programParticipationId, $consultationSessionId);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        
        $minStartTime = $this->dateTimeImmutableOfQueryRequest("minStartTime");
        $maxEndTime = $this->dateTimeImmutableOfQueryRequest("maxEndTime");
        $containParticipantFeedback = $this->filterBooleanOfQueryRequest("containParticipantFeedback");
        $containConsultantFeedback = $this->filterBooleanOfQueryRequest('containConsultantFeedback');

        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($minStartTime)
                ->setMaxEndTime($maxEndTime)
                ->setContainParticipantFeedback($containParticipantFeedback)
                ->setContainConsultantFeedback($containConsultantFeedback);
        
        $consultationSessions = $service->showAll($this->firmId(), $this->clientId(), $programParticipationId, $this->getPage(), $this->getPageSize(), $consultationSessionFilter);
        
        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTime(),
                "endTime" => $consultationSession->getEndTime(),
                "hasParticipantFeedback" => $consultationSession->hasParticipantFeedback(),
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
