<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\IContainParticipantReport;
use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\Model\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\Schedule;

class BookedMentoringSlot implements IContainParticipantReport, ContainSchedule
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

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
     * @var MentoringSlot
     */
    protected $mentoringSlot;

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

    public function __construct(Participant $participant, string $id, MentoringSlot $mentoringSlot)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->cancelled = false;
        $this->mentoringSlot = $mentoringSlot;
        $this->mentoring = new Mentoring($id);
        $this->participant->assertNoConflictWithScheduledOrPotentialSchedule($this);
    }

    public function isActive(): bool
    {
        return !$this->cancelled;
    }

    public function isActiveBookingCorrespondWithParticipant(Participant $participant): bool
    {
        return !$this->cancelled && $this->participant === $participant;
    }

    public function belongsToParticipant(Participant $participant): bool
    {
        return $this->participant === $participant;
    }
    
    public function assertManageableByParticipant(Participant $participant): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: booking already cancelled');
        }
        if ($this->participant !== $participant) {
            throw RegularException::forbidden('forbidden: can only managed owned booking slot');
        }
    }

    public function cancel(): void
    {
        $this->mentoringSlot->assertCancelBookingAllowed();
        $this->cancelled = true;
    }

    public function submitReport(int $mentorRating, FormRecordData $formRecordData): void
    {
        $this->mentoringSlot->processReportIn($this, $formRecordData, $mentorRating);
    }

    public function processReport(Form $form, FormRecordData $formRecordData, int $mentorRating): void
    {
        $this->mentoring->submitParticipantReport($form, $formRecordData, $mentorRating);
    }

    public function aScheduledOrPotentialScheduleInConflictWith(ContainSchedule $other): bool
    {
        return $other !== $this && !$this->cancelled && $this->mentoringSlot->scheduleInConflictWith($other);
    }

    public function scheduleIntersectWith(Schedule $other): bool
    {
        return $this->mentoringSlot->scheduleIntersectWith($other);
    }

}
