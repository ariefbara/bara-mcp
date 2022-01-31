<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;

use Personnel\Domain\Model\Firm\ContainMentorReport;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class NegotiatedMentoring implements ContainMentorReport
{

    /**
     * 
     * @var MentoringRequest
     */
    protected $mentoringRequest;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

    public function __construct(MentoringRequest $mentoringRequest, string $id)
    {
        $this->mentoringRequest = $mentoringRequest;
        $this->id = $id;
        $this->mentoring = new Mentoring($id);
    }
    
    public function assertBelongsToMentor(ProgramConsultant $mentor): void
    {
        if (!$this->mentoringRequest->belongsToMentor($mentor)) {
            throw RegularException::forbidden('forbidden: can only manage owned negotiated mentoring');
        }
    }

    public function submitReport(FormRecordData $formRecordData, ?int $participantRating): void
    {
        $this->mentoringRequest->processMentoringReport($this, $formRecordData, $participantRating);
    }

    public function processReport(Form $form, FormRecordData $formRecordData, ?int $participantRating): void
    {
        $this->mentoring->submitMentorReport($form, $formRecordData, $participantRating);
    }

}
