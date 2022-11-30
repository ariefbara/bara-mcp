<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DeclaredMentoring as DeclaredMentoring2;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Service\PersonnelFileInfoFinder;
use Personnel\Domain\Task\Mentor\ApproveParticipantMentoringDeclarationTask;
use Personnel\Domain\Task\Mentor\CancelMentoringDeclarationTask;
use Personnel\Domain\Task\Mentor\DeclareMentoringPayload;
use Personnel\Domain\Task\Mentor\DeclareMentoringTask;
use Personnel\Domain\Task\Mentor\DenyParticipantMentoringDeclarationTask;
use Personnel\Domain\Task\Mentor\SubmitDeclaredMentoringReportTask;
use Personnel\Domain\Task\Mentor\SubmitMentoringReportPayload;
use Personnel\Domain\Task\Mentor\UpdateDeclaredMentoringPayload;
use Personnel\Domain\Task\Mentor\UpdateDeclaredMentoringTask;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Participant\DeclaredMentoring;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\SharedModel\Mentoring\MentorReport;
use Query\Domain\Task\Personnel\ShowDeclaredMentoringTask;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\ValueObject\ScheduleData;

class DeclaredMentoringController extends PersonnelBaseController
{
    
    public function declare($mentorId)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        
        $participantId = $this->stripTagsInputRequest('participantId');
        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $endTime = $this->dateTimeImmutableOfInputRequest('endTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $scheduleData = new ScheduleData($startTime, $endTime, $mediaType, $location);
        
        $payload = new DeclareMentoringPayload($participantId, $consultationSetupId, $scheduleData);
        
        $task = new DeclareMentoringTask(
                $declaredMentoringRepository, $participantRepository, $consultationSetupRepository, $payload);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);

        $declaredMentoring = $this->buildAndExecuteShowTask($task->declaredMentoringId);        
        return $this->commandCreatedResponse($this->arrayDataOfDeclaredMentoring($declaredMentoring));
    }
    
    public function update($mentorId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $endTime = $this->dateTimeImmutableOfInputRequest('endTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $scheduleData = new ScheduleData($startTime, $endTime, $mediaType, $location);
        $payload = new UpdateDeclaredMentoringPayload($id, $scheduleData);
        
        $task = new UpdateDeclaredMentoringTask($declaredMentoringRepository, $payload);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        
        return $this->show($id);
    }
    
    public function cancel($mentorId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        $task = new CancelMentoringDeclarationTask($declaredMentoringRepository, $id);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        return $this->show($id);
    }
    
    public function approve($mentorId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        $task = new ApproveParticipantMentoringDeclarationTask($declaredMentoringRepository, $id);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        return $this->show($id);
    }
    
    public function deny($mentorId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        $task = new DenyParticipantMentoringDeclarationTask($declaredMentoringRepository, $id);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        return $this->show($id);
    }
    
    public function submitReport($mentorId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        
        $participantRating = $this->integerOfInputRequest('participantRating');
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = (new PersonnelFileInfoFinder($fileInfoRepository, $this->firmId(), $this->personnelId()));
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $payload = new SubmitMentoringReportPayload($id, $participantRating, $formRecordData);
        
        $task = new SubmitDeclaredMentoringReportTask($declaredMentoringRepository, $payload);
        $this->executeMentorTaskInPersonnelContext($mentorId, $task);
        return $this->show($id);
    }
    
    public function show($id)
    {
        $declaredMentoring = $this->buildAndExecuteShowTask($id);
        return $this->singleQueryResponse($this->arrayDataOfDeclaredMentoring($declaredMentoring));
    }
    
    protected function buildAndExecuteShowTask($id): DeclaredMentoring
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring::class);
        $task = new ShowDeclaredMentoringTask($declaredMentoringRepository, $id);
        $this->executePersonnelQueryTask($task);
        return $task->result;
    }
    
    protected function arrayDataOfDeclaredMentoring(DeclaredMentoring $declaredMentoring): array
    {
        $mentorFeedbackForm = $declaredMentoring
                ->getConsultationSetup()
                ->getConsultantFeedbackForm();
        return [
            'id' => $declaredMentoring->getId(),
            'startTime' => $declaredMentoring->getStartTimeString(),
            'endTime' => $declaredMentoring->getEndTimeString(),
            'mediaType' => $declaredMentoring->getMediaType(),
            'location' => $declaredMentoring->getLocation(),
            'declaredStatus' => $declaredMentoring->getDeclaredStatusDisplayValue(),
            'consultationSetup' => [
                'id' => $declaredMentoring->getConsultationSetup()->getId(),
                'name' => $declaredMentoring->getConsultationSetup()->getName(),
                'mentorFeedbackForm' => $this->arrayDataOfFeedbackForm($mentorFeedbackForm),
            ],
            'participant' => [
                'id' => $declaredMentoring->getParticipant()->getId(),
                'client' => $this->arrayDataOfClient($declaredMentoring->getParticipant()->getClientParticipant()),
                'team' => $this->arrayDataOfTeam($declaredMentoring->getParticipant()->getTeamParticipant()),
                'user' => $this->arrayDataOfUser($declaredMentoring->getParticipant()->getUserParticipant()),
            ],
            'mentorReport' => $this->arrayDataOfMentorReport($declaredMentoring->getMentoring()->getMentorReport()),
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
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null: [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }
    
}
