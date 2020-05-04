<?php

namespace Client\Domain\Model\Firm\Program;

use Client\Domain\Model\Firm\{
    FeedbackForm,
    Program
};
use DateInterval;
use DateTimeImmutable;
use Resources\Domain\ValueObject\DateTimeInterval;
use Shared\Domain\Model\{
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
    protected $participantFeedbackForm;

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

    public function createFormRecordFormParticipantFeedback(string $id, FormRecordData $formRecordData): FormRecord
    {
        return $this->participantFeedbackForm->createFormRecord($id, $formRecordData);
    }

}
