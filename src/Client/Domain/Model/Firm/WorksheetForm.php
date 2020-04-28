<?php

namespace Client\Domain\Model\Firm;

use Client\Domain\Model\Firm;
use Shared\Domain\Model\{
    Form,
    FormRecord,
    FormRecordData
};

class WorksheetForm
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
    protected $removed = false;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getForm(): Form
    {
        return $this->form;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    public function createFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        return new FormRecord($this->form, $formRecordId, $formRecordData);
    }

}
