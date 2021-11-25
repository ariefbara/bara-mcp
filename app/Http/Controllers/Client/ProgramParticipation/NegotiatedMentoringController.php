<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Participant\Domain\Model\Participant\MentoringRequest\NegotiatedMentoring as NegotiatedMentoring2;
use Participant\Domain\Service\ClientFileInfoFinder;
use Participant\Domain\Task\Participant\SubmitMentoringReportPayload;
use Participant\Domain\Task\Participant\SubmitNegotiatedMentoringReportTask;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;
use Query\Domain\Task\Participant\ShowNegotiatedMentoringTask;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class NegotiatedMentoringController extends ClientParticipantBaseController
{
    public function submitReport($programParticipationId, $id)
    {
        $negotiatedMentoringRepository = $this->em->getRepository(NegotiatedMentoring2::class);
        
        $mentorRating = $this->integerOfInputRequest('mentorRating');
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new ClientFileInfoFinder($fileInfoRepository, $this->firmId(), $this->clientId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder));
        
        $payload = new SubmitMentoringReportPayload($id, $mentorRating, $formRecordData);
        $task = new SubmitNegotiatedMentoringReportTask($negotiatedMentoringRepository, $payload);
        $this->executeParticipantTask($clientParticipantId, $task);
        
        return $this->show($programParticipationId, $id);
    }
    
    public function show($programParticipationId, $id)
    {
        $negotiatedMentoringRepository = $this->em->getRepository(NegotiatedMentoring::class);
        $task = new ShowNegotiatedMentoringTask($negotiatedMentoringRepository, $id);
        $this->executeQueryParticipantTask($clientParticipantId, $task);
        
        return $this->singleQueryResponse($this->arrayDataOfNegotiatedMentoring($task->result));
    }
    
    protected function arrayDataOfNegotiatedMentoring(NegotiatedMentoring $negotiatedMentoring): array
    {
        $participantFeedbackform = $negotiatedMentoring
                ->getMentoringRequest()
                ->getConsultationSetup()
                ->getParticipantFeedbackForm();
        return [
            'id' => $negotiatedMentoring->getId(),
            'mentoringRequest' => [
                'id' => $negotiatedMentoring->getMentoringRequest()->getId(),
                'consultationSetup' => [
                    'id' => $negotiatedMentoring->getMentoringRequest()->getConsultationSetup()->getId(),
                    'participantFeedbackForm' => $this->arrayDataOfFeedbackForm($participantFeedbackform),
                ],
            ],
            'participantReport' => $this->arrayDataOfParticipantReport($negotiatedMentoring->getParticipantReport()),
        ];
    }
    protected function arrayDataOfFeedbackForm(?FeedbackForm $feedbackForm): ?array
    {
        if (empty($feedbackForm)) {
            return null;
        }
        return (new FormToArrayDataConverter())->convert($feedbackForm);
    }
    protected function arrayDataOfParticipantReport(?ParticipantReport $participantReport): ?array
    {
        if (empty($participantReport)) {
            return null;
        }
        $participantReportData = (new FormRecordToArrayDataConverter())->convert($participantReport);
        $participantReportData['mentorRating'] = $participantReport->getMentorRating();
        return $participantReportData;
    }
}
