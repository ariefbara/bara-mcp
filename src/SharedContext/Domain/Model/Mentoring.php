<?php

namespace SharedContext\Domain\Model;

use Resources\Uuid;
use SharedContext\Domain\Model\Mentoring\MentorReport;
use SharedContext\Domain\Model\Mentoring\ParticipantReport;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class Mentoring
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ParticipantReport
     */
    protected $participantReport;

    /**
     * 
     * @var MentorReport
     */
    protected $mentorReport;
    
    public function __construct(string $id)
    {
        $this->id = $id;
    }
    
    public function submitMentorReport(Form $form, FormRecordData $formRecordData, int $participantRating): void
    {
        if (isset($this->mentorReport)) {
            $this->mentorReport->update($participantRating, $form, $formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $this->mentorReport = new MentorReport($this, $id, $participantRating, $form, $formRecordData);
        }
    }
    
    public function submitParticipantReport(Form $form, FormRecordData $formRecordData, int $mentorRating): void
    {
        if (isset($this->participantReport)) {
            $this->participantReport->update($mentorRating, $form, $formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $this->participantReport = new ParticipantReport($this, $id, $mentorRating, $form, $formRecordData);
        }
    }


}
