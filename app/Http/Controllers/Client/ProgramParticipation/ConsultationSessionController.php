<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\{
    Client\ClientBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter
};
use Client\{
    Application\Service\Client\ProgramParticipation\ConsultationSession\ParticipantFeedbackSet,
    Application\Service\Client\ProgramParticipation\ConsultationSessionView,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Domain\Model\Client\ProgramParticipation\ConsultationSession,
    Domain\Model\Client\ProgramParticipation\ProgramParticipationFileInfo,
    Domain\Service\ProgramParticipationFileInfoFinder
};
use Shared\Domain\Model\FormRecordData;

class ConsultationSessionController extends ClientBaseController
{

    public function setParticipantFeedback($programParticipationId, $consultationSessionId)
    {
        $service = $this->buildSetParticipantFeedbackService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $formRecordData = $this->getFormRecordData($programParticipationCompositionId);
        $service->execute($programParticipationCompositionId, $consultationSessionId, $formRecordData);

        return $this->show($programParticipationId, $consultationSessionId);
    }

    protected function getFormRecordData(ProgramParticipationCompositionId $programParticipationCompositionId): FormRecordData
    {
        $programParticipationFileInfoRepository = $this->em->getRepository(ProgramParticipationFileInfo::class);
        $programParticipationCompositionId;

        $fileInfoFinder = new ProgramParticipationFileInfoFinder(
                $programParticipationFileInfoRepository, $programParticipationCompositionId);
        return (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
    }

    public function show($programParticipationId, $consultationSessionId)
    {
        $service = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $consultationSession = $service->showById($programParticipationCompositionId, $consultationSessionId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $consultationSessions = $service->showAll(
                $programParticipationCompositionId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTimeString(),
                "endTime" => $consultationSession->getEndTimeString(),
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
            "startTime" => $consultationSession->getStartTimeString(),
            "endTime" => $consultationSession->getEndTimeString(),
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
            "participantFeedback" => $participantFeedback,
        ];
    }

    protected function buildViewService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        return new ConsultationSessionView($consultationSessionRepository);
    }

    protected function buildSetParticipantFeedbackService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        return new ParticipantFeedbackSet($consultationSessionRepository);
    }

}
