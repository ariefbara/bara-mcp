<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\{
    Client\TeamMembership\TeamMembershipBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    FormToArrayDataConverter
};
use Participant\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ConsultationSession\SubmitReport,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\ConsultationSession as ConsultationSession2,
    Domain\Service\TeamFileInfoFinder
};
use Query\{
    Application\Service\Firm\Team\ProgramParticipation\ViewConsultationSession,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends \App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController
{

    public function submitReport($teamId, $teamProgramParticipationId, $consultationSessionId)
    {
        $service = $this->buildSubmitReport();

        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new TeamFileInfoFinder(
                $fileInfoRepository, $this->firmId(), $this->clientId(), $teamId);
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();

        $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $consultationSessionId, $formRecordData);

        return $this->show($teamId, $teamProgramParticipationId, $consultationSessionId);
    }

    public function show($teamId, $teamProgramParticipationId, $consultationSessionId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $consultationSession = $service->showById($teamId, $consultationSessionId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setContainParticipantFeedback($this->filterBooleanOfQueryRequest("containParticipantFeedback"));

        $consultationSessions = $service->showAll(
                $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize(), $consultationSessionFilter);

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

    protected function buildSubmitReport()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);

        return new SubmitReport($consultationSessionRepository, $teamMembershipRepository);
    }

}
