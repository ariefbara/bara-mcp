<?php

namespace Participant\Domain\Model\DependencyEntity\Firm\Program;

use DateInterval;
use DateTimeImmutable;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\ {
    Firm\FeedbackForm,
    SharedEntity\FormRecord,
    SharedEntity\FormRecordData
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
    
    public function programIdEquals(string $programId): bool
    {
        return $this->programId == $programId;
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
