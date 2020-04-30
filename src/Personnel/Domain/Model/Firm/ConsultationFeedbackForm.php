<?php

namespace Personnel\Domain\Model\Firm;

use Personnel\Domain\Model\Firm;
use Shared\Domain\Model\ {
    Form,
    FormRecord,
    FormRecordData
};

class ConsultationFeedbackForm
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

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }
    
    public function createFormRecord(string $id, FormRecordData $formRecordData): FormRecord
    {
        return new FormRecord($this->form, $id, $formRecordData);
    }
}
