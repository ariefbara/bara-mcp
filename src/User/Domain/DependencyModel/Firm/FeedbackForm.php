<?php

namespace User\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecord,
    FormRecordData
};
use User\Domain\DependencyModel\Firm;

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
        
    }
    
    public function createFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        return $this->form->createFormRecord($formRecordId, $formRecordData);
    }

}
