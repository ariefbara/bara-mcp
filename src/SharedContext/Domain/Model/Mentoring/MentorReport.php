<?php

namespace SharedContext\Domain\Model\Mentoring;

use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class MentorReport
{

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

    /**
     * 
     * @var string
     */
    protected $id;
    
    /**
     * 
     * @var int|null
     */
    protected $participantRating;

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;
    
    public function __construct(
            Mentoring $mentoring, string $id, ?int $participantRating, Form $form, FormRecordData $formRecordData)
    {
        $this->mentoring = $mentoring;
        $this->id = $id;
        $this->participantRating = $participantRating;
        $this->formRecord = new FormRecord($form, $id, $formRecordData);
    }
    
    public function update(?int $participantRating, Form $form, FormRecordData $formRecordData): void
    {
        $this->participantRating = $participantRating;
        $this->formRecord->update($formRecordData);
    }

}
