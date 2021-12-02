<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\ContainMentorReport;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;

class DeclaredMentoring implements ContainMentorReport
{

    /**
     * 
     * @var ProgramConsultant
     */
    protected $mentor;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Participant
     */
    protected $participant;

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

    protected function setSchedue(ScheduleData $scheduleData): void
    {
        $this->schedule = new Schedule($scheduleData);
        if (!$this->schedule->isAlreadyPassed()) {
            throw RegularException::badRequest('bad request: declared mentoring must be a past event');
        }
    }

    public function __construct(
            ProgramConsultant $mentor, string $id, Participant $participant, ConsultationSetup $consultationSetup,
            ScheduleData $scheduleData)
    {
        $this->mentor = $mentor;
        $this->id = $id;
        $this->participant = $participant;
        $this->consultationSetup = $consultationSetup;
        $this->declaredStatus = new DeclaredMentoringStatus(DeclaredMentoringStatus::DECLARED_BY_MENTOR);
        $this->setSchedue($scheduleData);
        $this->mentoring = new Mentoring($id);
    }

    public function assertManageableByMentor(ProgramConsultant $mentor): void
    {
        if ($this->mentor !== $mentor) {
            throw RegularException::forbidden('forbidden: can only manage owned asset');
        }
    }

    public function update(ScheduleData $scheduleData): void
    {
        if (!$this->declaredStatus->statusEquals(DeclaredMentoringStatus::DECLARED_BY_MENTOR)) {
            throw RegularException::forbidden('forbidden: can only update declaration in declared by mentor state');
        }
        $this->setSchedue($scheduleData);
    }

    public function cancel(): void
    {
        $this->declaredStatus = $this->declaredStatus->cancelMentorDeclaration();
    }

    public function approveParticipantDeclaration(): void
    {
        $this->declaredStatus = $this->declaredStatus->approveParticipantDeclaration();
    }

    public function denyParticipantDeclaration(): void
    {
        $this->declaredStatus = $this->declaredStatus->denyParticipantDeclaration();
    }

    public function submitReport(FormRecordData $formRecordData, ?int $participantRating): void
    {
        $reportableStatus = [
            DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
            DeclaredMentoringStatus::APPROVED_BY_MENTOR,
            DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
        ];
        if (!$this->declaredStatus->statusIn($reportableStatus)) {
            throw RegularException::forbidden('forbidden: can only submit report on declared or approved status');
        }
        $this->consultationSetup->processReportIn($this, $formRecordData, $participantRating);
    }

    public function processReport(Form $form, FormRecordData $formRecordData, ?int $participantRating): void
    {
        $this->mentoring->submitMentorReport($form, $formRecordData, $participantRating);
    }

}
