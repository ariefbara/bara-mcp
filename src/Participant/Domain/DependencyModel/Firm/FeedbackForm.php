<?php

namespace Participant\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class FeedbackForm
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Form
     */
    protected $form;

    /**
     *
     * @var bool
     */
    protected $removed;

    protected function __construct()
    {
    }

    public function createFormRecord(string $id, FormRecordData $formRecordData): FormRecord
    {
        return new FormRecord($this->form, $id, $formRecordData);
    }
    
    public function processReportIn(
            IContainParticipantReport $mentoring, FormRecordData $formRecordData, int $mentorRating): void
    {
        $mentoring->processReport($this->form, $formRecordData, $mentorRating);
    }

}
