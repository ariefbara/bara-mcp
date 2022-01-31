<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant\DeclaredMentoring as DeclaredMentoring2;
use Participant\Domain\Service\TeamFileInfoFinder;
use Participant\Domain\Task\Participant\ApproveMentorMentoringDeclarationTask;
use Participant\Domain\Task\Participant\CancelMentoringDeclarationTask;
use Participant\Domain\Task\Participant\DeclareMentoringPayload;
use Participant\Domain\Task\Participant\DeclareMentoringTask;
use Participant\Domain\Task\Participant\DenyMentorMentoringDeclarationTask;
use Participant\Domain\Task\Participant\SubmitDeclaredMentoringReportTask;
use Participant\Domain\Task\Participant\SubmitMentoringReportPayload;
use Participant\Domain\Task\Participant\UpdateDeclaredMentoringPayload;
use Participant\Domain\Task\Participant\UpdateDeclaredMentoringTask;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Participant\DeclaredMentoring;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;
use Query\Domain\Task\Participant\ShowDeclaredMentoringTask;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\ValueObject\ScheduleData;

class DeclaredMentoringController extends AsTeamMemberBaseController
{
    
    public function declare($teamId, $teamProgramParticipationId)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        $mentorRepository = $this->em->getRepository(Consultant::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        
        $mentorId = $this->stripTagsInputRequest('mentorId');
        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $endTime = $this->dateTimeImmutableOfInputRequest('endTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $scheduleData = new ScheduleData($startTime, $endTime, $mediaType, $location);
        
        $payload = new DeclareMentoringPayload($mentorId, $consultationSetupId, $scheduleData);
        
        $task = new DeclareMentoringTask($declaredMentoringRepository, $mentorRepository, $consultationSetupRepository, $payload);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        
        return $this->show($teamId, $teamProgramParticipationId, $task->declaredMentoringId);
    }
    
    public function update($teamId, $teamProgramParticipationId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $endTime = $this->dateTimeImmutableOfInputRequest('endTime');
        $mediaType = $this->stripTagsInputRequest('mediaType');
        $location = $this->stripTagsInputRequest('location');
        $scheduleData = new ScheduleData($startTime, $endTime, $mediaType, $location);
        
        $payload = new UpdateDeclaredMentoringPayload($id, $scheduleData);
        $task = new UpdateDeclaredMentoringTask($declaredMentoringRepository, $payload);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        return $this->show($teamId, $teamProgramParticipationId, $id);
    }
    
    public function cancel($teamId, $teamProgramParticipationId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        $task = new CancelMentoringDeclarationTask($declaredMentoringRepository, $id);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        return $this->show($teamId, $teamProgramParticipationId, $id);
    }
    
    public function approve($teamId, $teamProgramParticipationId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        $task = new ApproveMentorMentoringDeclarationTask($declaredMentoringRepository, $id);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        return $this->show($teamId, $teamProgramParticipationId, $id);
    }
    
    public function deny($teamId, $teamProgramParticipationId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        $task = new DenyMentorMentoringDeclarationTask($declaredMentoringRepository, $id);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        return $this->show($teamId, $teamProgramParticipationId, $id);
    }
    
    public function submitReport($teamId, $teamProgramParticipationId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring2::class);
        
        $mentorRating = $this->integerOfInputRequest('mentorRating');
        
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new TeamFileInfoFinder($fileInfoRepository, $teamId);
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        
        $payload = new SubmitMentoringReportPayload($id, $mentorRating, $formRecordData);
        $task = new SubmitDeclaredMentoringReportTask($declaredMentoringRepository, $payload);
        $this->executeTeamParticipantTask($teamId, $teamProgramParticipationId, $task);
        return $this->show($teamId, $teamProgramParticipationId, $id);
    }
    
    public function show($teamId, $teamProgramParticipationId, $id)
    {
        $declaredMentoringRepository = $this->em->getRepository(DeclaredMentoring::class);
        $task = new ShowDeclaredMentoringTask($declaredMentoringRepository, $id);
        $this->executeTeamParticipantQueryTask($teamId, $teamProgramParticipationId, $task);
        
        return $this->singleQueryResponse($this->arrayDataOfDeclaredMentoring($task->result));
    }
    
    protected function arrayDataOfDeclaredMentoring(DeclaredMentoring $declaredMentoring): array
    {
        $participantFeedbackForm = $declaredMentoring
                ->getConsultationSetup()
                ->getParticipantFeedbackForm();
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
                'participantFeedbackForm' => $this->arrayDataOfFeedbackForm($participantFeedbackForm),
            ],
            'mentor' => [
                'id' => $declaredMentoring->getMentor()->getId(),
                'personnel' => [
                    'id' => $declaredMentoring->getMentor()->getPersonnel()->getId(),
                    'name' => $declaredMentoring->getMentor()->getPersonnel()->getName(),
                ],
            ],
            'participantReport' => $this->arrayDataOfParticipantReport($declaredMentoring->getMentoring()->getParticipantReport()),
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
