<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest\NegotiatedMentoring as NegotiatedMentoring2;
use Personnel\Domain\Service\PersonnelFileInfoFinder;
use Personnel\Domain\Task\Mentor\SubmitMentoringReportPayload;
use Personnel\Domain\Task\Mentor\SubmitNegotiatedMentoringReportTask;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use Query\Domain\SharedModel\Mentoring\MentorReport;
use Query\Domain\Task\Personnel\ShowNegotiatedMentoringTask;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class NegotiatedMentoringController extends PersonnelBaseController
{
    public function submitReport($mentorId, $id)
    {
        $negotiatedMentoringRepository = $this->em->getRepository(NegotiatedMentoring2::class);
        
        $participantRating = $this->integerOfInputRequest('participantRating');
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new PersonnelFileInfoFinder($fileInfoRepository, $this->firmId(), $this->personnelId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $payload = new SubmitMentoringReportPayload($id, $participantRating, $formRecordData);
        
        $task = new SubmitNegotiatedMentoringReportTask($negotiatedMentoringRepository, $payload);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        
        return $this->show($id);
    }
    
    public function show($id)
    {
        $negotiatedMentoringRepository = $this->em->getRepository(NegotiatedMentoring::class);
        $task = new ShowNegotiatedMentoringTask($negotiatedMentoringRepository, $id);
        $this->executePersonnelQueryTask($task);
        return $this->singleQueryResponse($this->arrayDataOfNegotiatedMentoring($task->result));
    }
    
    protected function arrayDataOfNegotiatedMentoring(NegotiatedMentoring $negotiatedMentoring): array
    {
        $mentorFeedbackForm = $negotiatedMentoring
                ->getMentoringRequest()
                ->getConsultationSetup()
                ->getConsultantFeedbackForm();
        return [
            'id' => $negotiatedMentoring->getId(),
            'mentoringRequest' => [
                'id' => $negotiatedMentoring->getMentoringRequest()->getId(),
                'consultationSetup' => [
                    'id' => $negotiatedMentoring->getMentoringRequest()->getConsultationSetup()->getId(),
                    'participantFeedbackForm' => $this->arrayDataOfFeedbackForm($mentorFeedbackForm),
                ],
            ],
            'mentorReport' => $this->arrayDataOfMentorReport($negotiatedMentoring->getMentorReport()),
        ];
    }
    protected function arrayDataOfFeedbackForm(?FeedbackForm $feedbackForm): ?array
    {
        if (empty($feedbackForm)) {
            return null;
        }
        return (new FormToArrayDataConverter())->convert($feedbackForm);
    }
    protected function arrayDataOfMentorReport(?MentorReport $mentorReport): ?array
    {
        if (empty($mentorReport)) {
            return null;
        }
        $participantReportData = (new FormRecordToArrayDataConverter())->convert($mentorReport);
        $participantReportData['participantRating'] = $mentorReport->getParticipantRating();
        return $participantReportData;
    }
}
