<?php

namespace Participant\Domain\Model\DependencyEntity\Firm;

use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecord,
    FormRecordData
};

class WorksheetForm
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
    protected $removed = false;

    protected function __construct()
    {
        ;
    }

    public function createFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        return new FormRecord($this->form, $formRecordId, $formRecordData);
    }

}
