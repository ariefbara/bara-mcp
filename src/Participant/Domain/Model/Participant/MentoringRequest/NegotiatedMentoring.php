<?php

namespace Participant\Domain\Model\Participant\MentoringRequest;

use Participant\Domain\DependencyModel\Firm\IContainParticipantReport;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\MentoringRequest;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class NegotiatedMentoring implements IContainParticipantReport
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
    
    public function assertManageableByParticipant(Participant $participant): void
    {
        if (!$this->mentoringRequest->belongsToParticipant($participant)) {
            throw RegularException::forbidden('forbidden: can only manage owned negotiated mentoring');
        }
    }
    
    public function submitReport(int $mentorRating, FormRecordData $formRecordData): void
    {
        $this->mentoringRequest->processReportInMentoring($this, $formRecordData, $mentorRating);
    }

    public function processReport(Form $form, FormRecordData $formRecordData, int $mentorRating): void
    {
        $this->mentoring->submitParticipantReport($form, $formRecordData, $mentorRating);
    }

}
