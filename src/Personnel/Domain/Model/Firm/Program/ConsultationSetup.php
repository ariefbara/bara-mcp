<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateInterval;
use DateTimeImmutable;
use Personnel\Domain\Model\Firm\FeedbackForm;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};

class ConsultationSetup
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
     * @var string
     */
    protected $name;

    /**
     *
     * @var int
     */
    protected $sessionDuration;

    /**
     *
     * @var FeedbackForm
     */
    protected $consultantFeedbackForm;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    protected function __construct()
    {
        ;
    }

    public function getSessionStartEndTimeOf(DateTimeImmutable $startTime): DateTimeInterval
    {
        $endTime = $startTime->add(new DateInterval("PT{$this->sessionDuration}M"));
        return new DateTimeInterval($startTime, $endTime);
    }
    
    public function createFormRecordForConsultantFeedback(string $id, FormRecordData $formRecordData): FormRecord
    {
        return $this->consultantFeedbackForm->createFormRecord($id, $formRecordData);
    }

}
