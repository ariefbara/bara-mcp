<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ConsultationSession\SubmitReport;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant\ConsultationSession as ConsultationSession2;
use Participant\Domain\Service\TeamFileInfoFinder;
use Participant\Domain\Task\Participant\CancelConsultationSessionTask;
use Participant\Domain\Task\Participant\DeclareConsultationSessionPayload;
use Participant\Domain\Task\Participant\DeclareConsultationSessionTask;
use Query\Application\Service\Firm\Team\ProgramParticipation\ViewConsultationSession;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ParticipantFeedback;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends AsTeamMemberBaseController
{
    
    public function declare($teamId, $teamProgramParticipationId)
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $mentorRepository = $this->em->getRepository(Consultant::class);
        
        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $mentorId = $this->stripTagsInputRequest('consultantId');
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $endTime = $this->dateTimeImmutableOfInputRequest('endTime');
        $media = $this->stripTagsInputRequest('media');
        $address = $this->stripTagsInputRequest('address');
        $payload = new DeclareConsultationSessionPayload(
                $consultationSetupId, $mentorId, $startTime, $endTime, $media, $address);
        
        $task = new DeclareConsultationSessionTask(
                $consultationSessionRepository, $consultationSetupRepository, $mentorRepository, $payload);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        
        $consultationSessionId = $task->declaredSessionId;
        $consultationSession = $this->buildViewService()->showById($teamId, $consultationSessionId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationSession($consultationSession));
    }
    
    public function cancel($teamId, $teamProgramParticipationId, $consultationSessionId) 
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        $task = new CancelConsultationSessionTask($consultationSessionRepository, $consultationSessionId);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        
        return $this->show($teamId, $teamProgramParticipationId, $consultationSessionId);
    }

    public function submitReport($teamId, $teamProgramParticipationId, $consultationSessionId)
    {
        $service = $this->buildSubmitReport();

        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new TeamFileInfoFinder($fileInfoRepository, $teamId);
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $mentorRating = $this->stripTagsInputRequest("mentorRating");

        $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $consultationSessionId, $formRecordData, $mentorRating);

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
                "media" => $consultationSession->getMedia(),
                "address" => $consultationSession->getAddress(),
                "sessionType" => $consultationSession->getSessionTypeDisplayValue(),
                "approvedByMentor" => $consultationSession->isApprovedByMentor(),
                "cancelled" => $consultationSession->isCancelled(),
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
        return [
            "id" => $consultationSession->getId(),
            "startTime" => $consultationSession->getStartTime(),
            "endTime" => $consultationSession->getEndTime(),
            "media" => $consultationSession->getMedia(),
            "address" => $consultationSession->getAddress(),
            "sessionType" => $consultationSession->getSessionTypeDisplayValue(),
            "approvedByMentor" => $consultationSession->isApprovedByMentor(),
            "cancelled" => $consultationSession->isCancelled(),
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
            "participantFeedback" => $this->arrayDataOfParticipantFeedback($consultationSession->getParticipantFeedback()),
        ];
    }
    protected function arrayDataOfFeedbackForm(FeedbackForm $feedbackForm): array
    {
        $data = (new FormToArrayDataConverter())->convert($feedbackForm);
        $data['id'] = $feedbackForm->getId();
        return $data;
    }
    protected function arrayDataOfParticipantFeedback(?ParticipantFeedback $participantFeedback): ?array
    {
        if (empty($participantFeedback)) {
            return null;
        }
        $result = (new FormRecordToArrayDataConverter())->convert($participantFeedback);
        $result["mentorRating"] = $participantFeedback->getMentorRating();
        return $result;
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
