<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use DateInterval;
use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\FeedbackForm;
use Participant\Domain\DependencyModel\Firm\IContainParticipantReport;
use Participant\Domain\DependencyModel\Firm\Program;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

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
     * @var bool
     */
    protected $removed = false;

    protected function __construct()
    {
        
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
    
    public function assertUsableInProgram(Program $program): void
    {
        if ($this->removed || $this->program !== $program) {
            throw RegularException::forbidden('forbidden: unuseable consultation setup, either inactive or belongs to other program');
        }
    }
    
    public function processReportIn(
            IContainParticipantReport $mentoring, FormRecordData $formRecordData, int $mentorRating): void
    {
        $this->participantFeedbackForm->processReportIn($mentoring, $formRecordData, $mentorRating);
    }

}
