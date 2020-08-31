<?php

namespace Participant\Domain\Model\DependencyEntity\Firm;

use SharedContext\Domain\Model\SharedEntity\{
    Form,
    FormRecord,
    FormRecordData
};

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
        ;
    }

    public function createFormRecord(string $id, FormRecordData $formRecordData): FormRecord
    {
        return new FormRecord($this->form, $id, $formRecordData);
    }

}
