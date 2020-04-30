<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateInterval;
use DateTimeImmutable;
use Personnel\Domain\Model\Firm\ {
    ConsultationFeedbackForm,
    Program
};
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
     * @var ConsultationFeedbackForm
     */
    protected $participantFeedbackForm;

    /**
     *
     * @var ConsultationFeedbackForm
     */
    protected $consultantFeedbackForm;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getSessionDuration(): int
    {
        return $this->sessionDuration;
    }

    function getParticipantFeedbackForm(): ConsultationFeedbackForm
    {
        return $this->participantFeedbackForm;
    }

    function getConsultantFeedbackForm(): ConsultationFeedbackForm
    {
        return $this->consultantFeedbackForm;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

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
