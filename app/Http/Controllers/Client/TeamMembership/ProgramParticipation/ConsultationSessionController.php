<?php

namespace App\Http\Controllers\Client\TeamMembership\ProgramParticipation;

use App\Http\Controllers\ {
    Client\TeamMembership\TeamMembershipBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    FormToArrayDataConverter
};
use Participant\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ConsultationSession\SubmitReport,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\ConsultationSession as ConsultationSession2,
    Domain\Model\TeamProgramParticipation,
    Domain\Service\TeamFileInfoFinder
};
use Query\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ViewConsultationSession,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Domain\Service\Firm\Program\Participant\ConsultationSessionFinder,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends TeamMembershipBaseController
{

    public function submitReport($teamMembershipId, $teamProgramParticipationId, $consultationSessionId)
    {
        $service = $this->buildSubmitReport();

        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new TeamFileInfoFinder(
                $fileInfoRepository, $this->firmId(), $this->clientId(), $teamMembershipId);
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();

        $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId,
                $consultationSessionId, $formRecordData);

        return $this->show($teamMembershipId, $teamProgramParticipationId, $consultationSessionId);
    }

    public function show($teamMembershipId, $teamProgramParticipationId, $consultationSessionId)
    {
        $service = $this->buildViewService();
        $consultationSession = $service->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId,
                $consultationSessionId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function showAll($teamMembershipId, $teamProgramParticipationId)
    {
        $service = $this->buildViewService();
        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("endTime"))
                ->setContainParticipantFeedback($this->filterBooleanOfQueryRequest("containParticipantFeedback"));

        $consultationSessions = $service->showAll(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $this->getPage(),
                $this->getPageSize(), $consultationSessionFilter);
        
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
        $consultationSessionFinder = new ConsultationSessionFinder($consultationSessionRepository);
        return new ViewConsultationSession($this->teamMembershipRepository(), $consultationSessionFinder);
    }

    protected function buildSubmitReport()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);

        return new SubmitReport($consultationSessionRepository, $teamMembershipRepository,
                $teamProgramParticipationRepository);
    }

}
