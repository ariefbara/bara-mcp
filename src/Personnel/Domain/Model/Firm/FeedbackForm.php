<?php

namespace Personnel\Domain\Model\Firm;

use Query\Domain\Model\Firm;
use Shared\Domain\Model\ {
    Form,
    FormRecord,
    FormRecordData
};

class FeedbackForm
{

    /**
     *
     * @var Firm
     */
    protected $firm;

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
        ;
    }
    
    public function createFormRecord(string $id, FormRecordData $formRecordData): FormRecord
    {
        return new FormRecord($this->form, $id, $formRecordData);
    }
}
