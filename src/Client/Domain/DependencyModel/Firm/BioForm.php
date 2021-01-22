<?php

namespace Client\Domain\DependencyModel\Firm;

use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class BioForm
{

    /**
     * 
     * @var string
     */
    protected $firmId;

    /**
     * 
     * @var Form
     */
    protected $form;

    /**
     * 
     * @var bool
     */
    protected $disabled;
    
    protected function __construct()
    {
        
    }
    
    public function createFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        if ($this->disabled) {
            $errorDetail = "forbidden: can only submit CV on active Form";
            throw RegularException::forbidden($errorDetail);
        }
        return $this->form->createFormRecord($formRecordId, $formRecordData);
    }
    
    public function belongsToFirm(string $firmId): bool
    {
        return $this->firmId === $firmId;
    }

}
