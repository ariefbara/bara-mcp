<?php

namespace Personnel\Domain\Model\Firm\Program;

use Personnel\Domain\Model\Firm\FeedbackForm;
use Personnel\Domain\Model\Firm\Personnel\IUsableInProgram;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class EvaluationPlan implements IUsableInProgram
{

    /**
     * 
     * @var string
     */
    protected $programId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $disabled;

    /**
     *
     * @var FeedbackForm
     */
    protected $reportForm;
    
    protected function __construct()
    {
        
    }
    
    public function assertUsableInProgram(string $programId): void
    {
        if ($this->disabled || $this->programId !== $programId) {
            throw RegularException::forbidden('forbidden: unusable evaluation plan');
        }
    }
    
    public function createFormRecord(string $id, FormRecordData $formRecordData): FormRecord
    {
        return $this->reportForm->createFormRecord($id, $formRecordData);
    }

}
