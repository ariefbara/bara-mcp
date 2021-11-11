<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\ContainMentorReport;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot\BookedMentoringSlot;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Resources\Exception\RegularException;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;

class MentoringSlot
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
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var Schedule
     */
    protected $schedule;

    /**
     * 
     * @var int
     */
    protected $capacity;

    /**
     * 
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     * 
     * @var ArrayCollection
     */
    protected $bookedSlots;
    
    protected function setSchedule(ScheduleData $scheduleData): void
    {
        $this->schedule = new Schedule($scheduleData);
        if (!$this->schedule->isUpcoming()) {
            throw RegularException::badRequest('bad request: mentoring slot must be an upcoming schedule');
        }
    }

    protected function setCapacity(int $capacity): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($capacity, 'bad request: mentoring slot capacity is mandatory');
        $this->capacity = $capacity;
    }

    public function __construct(
            ProgramConsultant $mentor, string $id, ConsultationSetup $consultationSetup,
            MentoringSlotData $mentoringSlotData)
    {
        $this->mentor = $mentor;
        $this->id = $id;
        $this->cancelled = false;
        $this->consultationSetup = $consultationSetup;
        $this->setSchedule($mentoringSlotData->getScheduleData());
        $this->setCapacity($mentoringSlotData->getCapacity());
    }
    
    public function isUpcomingSchedule(): bool
    {
        return $this->schedule->isUpcoming();
    }

    public function belongsToMentor(ProgramConsultant $mentor): bool
    {
        return $this->mentor === $mentor;
    }

    public function update(MentoringSlotData $mentoringSlotData): void
    {
        if (!$this->schedule->isUpcoming()) {
            throw RegularException::forbidden('forbidden: unable to update schedule that already passed');
        }
        $p = function (BookedMentoringSlot $bookedMentoringSlot) {
            return $bookedMentoringSlot->isActiveBooking();
        };
        if (!$this->bookedSlots->filter($p)->isEmpty()) {
            throw RegularException::forbidden(
                    'bad request: unable to change mentoring slot setting because an active booking is exist');
        }
        $this->setSchedule($mentoringSlotData->getScheduleData());
        $this->setCapacity($mentoringSlotData->getCapacity());
    }

    public function cancel(): void
    {
        if (!$this->schedule->isUpcoming()) {
            throw RegularException::forbidden('forbidden: can only cancel an upcoming schedule');
        }
        $this->cancelled = true;
        
        $p = function (BookedMentoringSlot $bookedMentoringSlot) {
            return $bookedMentoringSlot->isActiveBooking();
        };
        foreach ($this->bookedSlots->filter($p)->getIterator() as $activeBookingSlot) {
            $activeBookingSlot->cancel();
        }
    }
    
    public function processReportIn(ContainMentorReport $mentoring, FormRecordData $formRecordData, ?int $participantRating): void
    {
        if (!$this->schedule->isAlreadyPassed()) {
            throw RegularException::forbidden('forbidden: can only process report on past mentoring');
        }
        $this->consultationSetup->processReportIn($mentoring, $formRecordData, $participantRating);
    }

}
