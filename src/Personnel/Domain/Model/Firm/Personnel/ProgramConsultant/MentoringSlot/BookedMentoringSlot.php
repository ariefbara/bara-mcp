<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;

use Personnel\Domain\Model\Firm\ContainMentorReport;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class BookedMentoringSlot implements ContainMentorReport
{

    /**
     * 
     * @var MentoringSlot
     */
    protected $mentoringSlot;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

    protected function __construct()
    {
        
    }
    
    public function assertManageableByMentor(ProgramConsultant $mentor): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: can only manage active booking');
        }
        if (!$this->mentoringSlot->belongsToMentor($mentor)) {
            throw RegularException::forbidden('forbidden: can only manage booking on owned slot');
        }
    }

    public function belongsToMentor(ProgramConsultant $mentor): bool
    {
        return $this->mentoringSlot->belongsToMentor($mentor);
    }

    public function cancel(): void
    {
        if (!$this->mentoringSlot->isUpcomingSchedule()) {
            throw RegularException::forbidden('forbidden: unable to cancel an already passed booking');
        }
        $this->cancelled = true;
    }

    public function isActiveBooking(): bool
    {
        return !$this->cancelled;
    }

    public function submitReport(FormRecordData $formRecordData, ?int $participantRating): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: unable to submit report on cancelled booking');
        }
        $this->mentoringSlot->processReportIn($this, $formRecordData, $participantRating);
    }

    public function processReport(Form $form, FormRecordData $formRecordData, ?int $participantRating): void
    {
        $this->mentoring->submitMentorReport($form, $formRecordData, $participantRating);
    }

}
