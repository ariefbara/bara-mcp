<?php

namespace SharedContext\Domain\Model\Mentoring;

use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ParticipantReport
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
    protected $mentorRating;

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;

    public function __construct(Mentoring $mentoring, string $id, ?int $mentorRating, Form $form,
            FormRecordData $formRecordData)
    {
        $this->mentoring = $mentoring;
        $this->id = $id;
        $this->mentorRating = $mentorRating;
        $this->formRecord = new FormRecord($form, $id, $formRecordData);
    }
    
    public function update(?int $mentorRating, Form $form, FormRecordData $formRecordData): void
    {
        $this->mentorRating = $mentorRating;
        $this->formRecord->update($formRecordData);
    }

}
