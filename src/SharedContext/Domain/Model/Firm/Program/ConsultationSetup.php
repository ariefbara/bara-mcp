<?php

namespace SharedContext\Domain\Model\Firm\Program;

use DateInterval;
use DateTimeImmutable;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\ {
    Firm\FeedbackForm,
    Firm\Program,
    SharedEntity\FormRecord,
    SharedEntity\FormRecordData
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

    public function programEquals(Program $program): bool
    {
        return $this->program === $program;
    }

    public function createFormRecordForParticipantFeedback(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        return $this->participantFeedbackForm->createFormRecord($formRecordId, $formRecordData);
    }

    public function getSessionStartEndTimeOf(DateTimeImmutable $startTime): DateTimeInterval
    {
        $endTime = $startTime->add(new DateInterval("PT{$this->sessionDuration}M"));
        return new DateTimeInterval($startTime, $endTime);
    }

}
