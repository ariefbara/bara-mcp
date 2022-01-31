<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\IContainParticipantReport;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;

class DeclaredMentoring implements IContainParticipantReport
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Consultant
     */
    protected $mentor;

    /**
     * 
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     * 
     * @var DeclaredMentoringStatus
     */
    protected $declaredStatus;

    /**
     * 
     * @var Schedule
     */
    protected $schedule;

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;
    
    protected function setSchedule(ScheduleData $scheduleData): void
    {
        $this->schedule = new Schedule($scheduleData);
        if (!$this->schedule->isAlreadyPassed()) {
            throw RegularException::badRequest('bad request: can only declared past mentoring event');
        }
    }

    public function __construct(
            Participant $participant, string $id, Consultant $mentor, ConsultationSetup $consultationSetup,
            ScheduleData $scheduleData)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->mentor = $mentor;
        $this->consultationSetup = $consultationSetup;
        $this->declaredStatus = new DeclaredMentoringStatus(DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT);
        $this->setSchedule($scheduleData);
        $this->mentoring = new Mentoring($id);
    }
    
    public function assertManageableByParticipant(Participant $participant): void
    {
        if ($this->participant !== $participant) {
            throw RegularException::forbidden('forbidden: can only manage owned declaration');
        }
    }
    
    public function update(ScheduleData $scheduleData): void
    {
        if (!$this->declaredStatus->statusEquals(DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT)) {
            throw RegularException::forbidden('forbidden: can only update declaration in declared by participant state');
        }
        $this->setSchedule($scheduleData);
    }
    
    public function cancel(): void
    {
        $this->declaredStatus = $this->declaredStatus->cancelParticipantDeclaration();
    }
    
    public function approveMentorDeclaration(): void
    {
        $this->declaredStatus = $this->declaredStatus->approveMentorDeclaration();
    }
    
    public function denyMentorDeclaration(): void
    {
        $this->declaredStatus = $this->declaredStatus->denyMentorDeclaration();
    }
    
    public function submitReport(FormRecordData $formRecordData, ?int $mentorRating): void
    {
        $reportableStates = [
            DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
            DeclaredMentoringStatus::APPROVED_BY_MENTOR,
            DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
        ];
        if (!$this->declaredStatus->statusIn($reportableStates)) {
            throw RegularException::forbidden('forbidden: declaration not in reportable state');
        }
        $this->consultationSetup->processReportIn($this, $formRecordData, $mentorRating);
    }

    public function processReport(Form $form, FormRecordData $formRecordData, int $mentorRating): void
    {
        $this->mentoring->submitParticipantReport($form, $formRecordData, $mentorRating);
    }

}
