<?php

namespace Participant\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ProfileForm
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
    
    protected function __construct()
    {
        
    }
    
    public function createFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        return $this->form->createFormRecord($formRecordId, $formRecordData);
    }
}
