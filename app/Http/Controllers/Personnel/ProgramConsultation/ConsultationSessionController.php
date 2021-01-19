<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use App\Http\Controllers\Personnel\PersonnelBaseController;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession\ConsultantFeedbackSet;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Personnel\Domain\Service\PersonnelFileInfoFinder;
use Query\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionView;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession as ConsultationSession2;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultantFeedback;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends PersonnelBaseController
{

    public function setConsultantFeedback($programConsultationId, $consultationSessionId)
    {
        $service = $this->buildSetConsultantFeedbackService();
        
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new PersonnelFileInfoFinder($fileInfoRepository, $this->firmId(), $this->personnelId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $participantRating = $this->stripTagsInputRequest("participantRating");
        
        $service->execute(
                $this->firmId(), $this->personnelId(), $programConsultationId, $consultationSessionId, $formRecordData, $participantRating);

        return $this->show($programConsultationId, $consultationSessionId);
    }

    public function show($programConsultationId, $consultationSessionId)
    {
        $service = $this->buildViewService();

        $consultationSession = $service->showById(
                $this->firmId(), $this->personnelId(), $programConsultationId, $consultationSessionId);

        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function showAll($programConsultationId)
    {
        $service = $this->buildViewService();
        
        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setContainParticipantFeedback($this->filterBooleanOfQueryRequest("containParticipantFeedback"))
                ->setContainConsultantFeedback($this->filterBooleanOfQueryRequest("containConsultantFeedback"));
        
        $consultationSessions = $service->showAll(
                $this->firmId(), $this->personnelId(), $programConsultationId, $this->getPage(), $this->getPageSize(),
                $consultationSessionFilter);

        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTime(),
                "endTime" => $consultationSession->getEndTime(),
                "media" => $consultationSession->getMedia(),
                "address" => $consultationSession->getAddress(),
                "participant" => [
                    "id" => $consultationSession->getParticipant()->getId(),
                    "client" => $this->arrayDataOfClient($consultationSession->getParticipant()->getClientParticipant()),
                    "user" => $this->arrayDataOfUser($consultationSession->getParticipant()->getUserParticipant()),
                    "team" => $this->arrayDataOfTeam($consultationSession->getParticipant()->getTeamParticipant()),
                ],
                "hasConsultantFeedback" => !empty($consultationSession->getConsultantFeedback()),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationSession(ConsultationSession2 $consultationSession): array
    {
        return [
            "id" => $consultationSession->getId(),
            "startTime" => $consultationSession->getStartTime(),
            "endTime" => $consultationSession->getEndTime(),
            "media" => $consultationSession->getMedia(),
            "address" => $consultationSession->getAddress(),
            "consultationSetup" => [
                "id" => $consultationSession->getConsultationSetup()->getId(),
                "name" => $consultationSession->getConsultationSetup()->getName(),
                "consultantFeedbackForm" => $this->arrayDataOfFeedbackForm(
                        $consultationSession->getConsultationSetup()->getConsultantFeedbackForm()),
            ],
            "participant" => [
                "id" => $consultationSession->getParticipant()->getId(),
                "client" => $this->arrayDataOfClient($consultationSession->getParticipant()->getClientParticipant()),
                "user" => $this->arrayDataOfUser($consultationSession->getParticipant()->getUserParticipant()),
                "team" => $this->arrayDataOfTeam($consultationSession->getParticipant()->getTeamParticipant()),
            ],
            "consultantFeedback" => $this->arrayDataOfConsultantFeedback($consultationSession->getConsultantFeedback()),
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant)? null: [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant)? null: [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant)? null: [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }
    protected function arrayDataOfFeedbackForm(FeedbackForm $feedbackForm): array
    {
        $data = (new FormToArrayDataConverter())->convert($feedbackForm);
        $data['id'] = $feedbackForm->getId();
        return $data;
    }
    protected function arrayDataOfConsultantFeedback(?ConsultantFeedback $consultantFeedback): ?array
    {
        if (empty($consultantFeedback)) {
            return null;
        }
        $result = (new FormRecordToArrayDataConverter())->convert($consultantFeedback);
        $result["participantRating"] = $consultantFeedback->getParticipantRating();
        return $result;
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
