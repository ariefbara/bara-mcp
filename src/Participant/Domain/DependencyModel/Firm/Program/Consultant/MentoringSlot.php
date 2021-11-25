<?php

namespace Participant\Domain\DependencyModel\Firm\Program\Consultant;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\IContainParticipantReport;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\BookedMentoringSlot;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\Schedule;

class MentoringSlot
{

    /**
     * 
     * @var Consultant
     */
    protected $consultant;

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
    
    protected function __construct()
    {
        
    }
    
    public function usableInProgram(Program $program): bool
    {
        return !$this->cancelled && $this->consultant->programEquals($program);
    }
    
    public function canAcceptBookingFrom(Participant $participant): bool
    {
        $capacityFilter = function(BookedMentoringSlot $bookedMentoringSlot) {
            return $bookedMentoringSlot->isActive();
        };
        $allowParticipantFilter = function(BookedMentoringSlot $bookedMentoringSlot) use($participant) {
            return $bookedMentoringSlot->isActiveBookingCorrespondWithParticipant($participant);
        };
        
        return $this->schedule->isUpcoming() 
                && $this->bookedSlots->filter($capacityFilter)->count() < $this->capacity 
                && $this->bookedSlots->filter($allowParticipantFilter)->isEmpty();
    }
    
    public function assertCancelBookingAllowed(): void
    {
        if (!$this->schedule->isUpcoming()) {
            throw RegularException::forbidden('forbidden: can only cancel booking on upcoming schedule');
        }
    }
    
    public function processReportIn(
            IContainParticipantReport $mentoring, FormRecordData $formRecordData, int $mentorRating): void
    {
        if (!$this->schedule->isAlreadyPassed()) {
            throw RegularException::forbidden('forbidden: can only submit report on past mentoring');
        }
        $this->consultationSetup->processReportIn($mentoring, $formRecordData, $mentorRating);
    }
    
    public function scheduleInConflictWith(Participant\ContainSchedule $mentoringSchedule): bool
    {
        return $mentoringSchedule->scheduleIntersectWith($this->schedule);
    }
    
    public function scheduleIntersectWith(Schedule $other): bool
    {
        return $this->schedule->intersectWith($other);
    }

}
