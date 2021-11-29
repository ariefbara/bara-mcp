<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest\NegotiatedMentoring;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;

class MentoringRequest implements ContainSchedule
{

    /**
     * 
     * @var ProgramConsultant
     */
    protected $mentor;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var MentoringRequestStatus
     */
    protected $requestStatus;

    /**
     * 
     * @var Schedule
     */
    protected $schedule;

    /**
     * 
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     * 
     * @var NegotiatedMentoring|null
     */
    protected $negotiatedMentoring;

    protected function __construct()
    {
        
    }
    
    public function belongsToMentor(ProgramConsultant $mentor): bool
    {
        return $this->mentor === $mentor;   
    }

    public function assertBelongsToMentor(ProgramConsultant $mentor): void
    {
        if ($this->mentor !== $mentor) {
            throw RegularException::forbidden('forbidden: can only managed owned mentoring request');
        }
    }

    public function reject(): void
    {
        $this->requestStatus = $this->requestStatus->reject();
    }

    public function approve(): void
    {
        if (!$this->schedule->isUpcoming()) {
            throw RegularException::forbidden('forbidden: can only approve upcoming schedule');
        }
        $this->requestStatus = $this->requestStatus->approve();
        $this->mentor->assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule($this);

        $this->negotiatedMentoring = new NegotiatedMentoring($this, $this->id);
    }

    public function offer(MentoringRequestData $mentoringRequestData): void
    {
        $startTime = $mentoringRequestData->getStartTime();
        $endTime = $this->consultationSetup->calculateMentoringScheduleEndTimeFrom($startTime);
        $mediaType = $mentoringRequestData->getMediaType();
        $location = $mentoringRequestData->getLocation();
        $scheduleData = new ScheduleData($startTime, $endTime, $mediaType, $location);

        $this->schedule = new Schedule($scheduleData);
        $this->requestStatus = $this->requestStatus->offer();

        if (!$this->schedule->isUpcoming()) {
            throw RegularException::forbidden('forbidden: can only offer upcoming schedule');
        }
        $this->mentor->assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule($this);
    }

    public function isScheduledOrOfferedRequestInConflictWith(ContainSchedule $otherEvent): bool
    {
        $exptectedStatusList = [
            MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT,
            MentoringRequestStatus::APPROVED_BY_MENTOR,
            MentoringRequestStatus::OFFERED,
        ];
        return $this !== $otherEvent
                && $this->requestStatus->statusIn($exptectedStatusList) 
                && $otherEvent->scheduleInConflictWith($this->schedule);
    }

    public function scheduleInConflictWith(Schedule $otherSchedule): bool
    {
        return $this->schedule->intersectWith($otherSchedule);
    }
    
    public function processMentoringReport(
            NegotiatedMentoring $negotiatedMentoring, FormRecordData $formRecordData, ?int $participantRating): void
    {
        if (!$this->schedule->isAlreadyPassed()) {
            throw RegularException::forbidden('forbidden: can only submit report on past mentoring');
        }
        $this->consultationSetup->processReportIn($negotiatedMentoring, $formRecordData, $participantRating);
    }
    
}
