<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateInterval;
use DateTimeImmutable;
use Personnel\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program;
use Resources\Domain\ValueObject\DateTimeInterval;
use Shared\Domain\Model\ {
    FormRecord,
    FormRecordData
};

class ConsultationSetup
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
