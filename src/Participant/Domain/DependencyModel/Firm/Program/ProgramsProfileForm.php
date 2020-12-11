<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Participant\Domain\DependencyModel\Firm\ProfileForm;
use Participant\Domain\DependencyModel\Firm\Program;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ProgramsProfileForm
{

    /**
     * 
     * @var Program
     */
    protected $program;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ProfileForm
     */
    protected $profileForm;

    /**
     * 
     * @var bool
     */
    protected $disabled;
    
    protected function __construct()
    {
        
    }
    
    public function programEquals(Program $program): bool
    {
        return $this->program === $program;
    }
    
    public function createFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        if ($this->disabled) {
            $errorDetail = "forbidden: can only submit profile from enabled template";
            throw RegularException::forbidden($errorDetail);
        }
        return $this->profileForm->createFormRecord($formRecordId, $formRecordData);
    }

}
