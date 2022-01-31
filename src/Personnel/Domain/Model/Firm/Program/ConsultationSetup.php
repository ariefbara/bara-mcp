<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateInterval;
use DateTimeImmutable;
use Personnel\Domain\Model\Firm\ContainMentorReport;
use Personnel\Domain\Model\Firm\FeedbackForm;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

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
    
    public function usableInProgram(string $programId): bool
    {
        return $this->programId === $programId && !$this->removed;
    }
    
    public function processReportIn(ContainMentorReport $mentoring, FormRecordData $formRecordData, ?int $participantRating): void
    {
        $this->consultantFeedbackForm->processReportIn($mentoring, $formRecordData, $participantRating);
    }
    
    public function calculateMentoringScheduleEndTimeFrom(\DateTimeImmutable $startTime): \DateTimeImmutable
    {
        return $startTime->add(new DateInterval("PT{$this->sessionDuration}M"));
    }
    
    public function assertUsableInProgram(string $programId): void
    {
        if (!$this->usableInProgram($programId)) {
            throw RegularException::forbidden('forbidden: can only use active consultation setup in same program');
        }
    }
    
}
