<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Participant\Application\Service\ClientParticipant\ConsultationSession\ParticipantFeedbackSet;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant\ConsultationSession as ConsultationSession2;
use Participant\Domain\Service\ClientFileInfoFinder;
use Participant\Domain\Task\Participant\CancelConsultationSessionTask;
use Participant\Domain\Task\Participant\DeclareConsultationSessionPayload;
use Participant\Domain\Task\Participant\DeclareConsultationSessionTask;
use Query\Application\Service\Firm\Client\ProgramParticipation\ViewConsultationSession;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ParticipantFeedback;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends ClientParticipantBaseController
{

    public function declare($programParticipationId)
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

        $task = new DeclareConsultationSessionTask($consultationSessionRepository, $consultationSetupRepository,
                $mentorRepository, $payload);
        $this->executeParticipantTask($programParticipationId, $task);
        
        $consultationSession= $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $programParticipationId, $task->declaredSessionId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function cancel($programParticipationId, $consultationSessionId)
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        $task = new CancelConsultationSessionTask($consultationSessionRepository, $consultationSessionId);
        $this->executeParticipantTask($programParticipationId, $task);
        
        return $this->show($programParticipationId, $consultationSessionId);
    }

    public function submitReport($programParticipationId, $consultationSessionId)
    {
        $service = $this->buildSetParticipantFeedbackService();
        $formRecordData = $this->getFormRecordData();
        $mentorRating = $this->stripTagsInputRequest("mentorRating");

        $service->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $consultationSessionId, $formRecordData,
                $mentorRating);

        return $this->show($programParticipationId, $consultationSessionId);
    }

    public function show($programParticipationId, $consultationSessionId)
    {
        $service = $this->buildViewService();
        $consultationSession = $service->showById($this->firmId(), $this->clientId(), $programParticipationId,
                $consultationSessionId);

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

        $consultationSessions = $service->showAll($this->firmId(), $this->clientId(), $programParticipationId,
                $this->getPage(), $this->getPageSize(), $consultationSessionFilter);

        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTime(),
                "endTime" => $consultationSession->getEndTime(),
                "media" => $consultationSession->getMedia(),
                "address" => $consultationSession->getAddress(),
                "cancelled" => $consultationSession->isCancelled(),
                "sessionType" => $consultationSession->getSessionTypeDisplayValue(),
                'approvedByMentor' => $consultationSession->isApprovedByMentor(),
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
            'approvedByMentor' => $consultationSession->isApprovedByMentor(),
            'cancelled' => $consultationSession->isCancelled(),
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
