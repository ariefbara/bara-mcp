<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\MentoringRequest\NegotiatedMentoring;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;

class MentoringRequest implements ContainSchedule
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
     * @var Consultant
     */
    protected $mentor;

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

    protected function setSchedule(MentoringRequestData $mentoringRequestData): void
    {
        $startTime = $mentoringRequestData->getStartTime();
        $endTime = $this->consultationSetup->calculateScheduleEndTime($startTime);
        $mediaType = $mentoringRequestData->getMediaType();
        $location = $mentoringRequestData->getLocation();
        $this->schedule = new Schedule(new ScheduleData($startTime, $endTime, $mediaType, $location));
        
        if (!$this->schedule->isUpcoming()) {
            throw RegularException::badRequest('bad request: can only request upcomming mentoring schedule');
        }
        $this->participant->assertNoConflictWithScheduledOrPotentialSchedule($this);
    }

    public function __construct(
            Participant $participant, string $id, Consultant $mentor, ConsultationSetup $consultationSetup, MentoringRequestData $mentoringRequestData)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->mentor = $mentor;
        $this->consultationSetup = $consultationSetup;
        $this->requestStatus = new MentoringRequestStatus(MentoringRequestStatus::REQUESTED);
        $this->setSchedule($mentoringRequestData);
    }

    public function assertManageableByParticipant(Participant $participant): void
    {
        if ($this->participant !== $participant) {
            throw RegularException::forbidden('forbidden: can only managed owned mentoring request');
        }
    }
    
    public function update(MentoringRequestData $mentoringRequestData): void
    {
        if ($this->requestStatus->isConcluded()) {
            throw RegularException::forbidden('forbidden: can only update active mentoring request');
        }
        $this->setSchedule($mentoringRequestData);
        $this->requestStatus = new MentoringRequestStatus(MentoringRequestStatus::REQUESTED);
    }

    public function cancel(): void
    {
        $this->requestStatus = $this->requestStatus->cancel();
    }
    
    public function accept(): void
    {
        if (!$this->schedule->isUpcoming()) {
            throw RegularException::forbidden('forbidden: can only accept upcoming schedule');
        }
        $this->requestStatus = $this->requestStatus->accept();
        
        $this->participant->assertNoConflictWithScheduledOrPotentialSchedule($this);
        
        $this->negotiatedMentoring = new NegotiatedMentoring($this, $this->id);
    }

    public function aScheduledOrPotentialScheduleInConflictWith(ContainSchedule $other): bool
    {
        return $other !== $this
                && $this->requestStatus->isScheduledOrPotentialSchedule()
                && $other->scheduleIntersectWith($this->schedule);
    }

    public function scheduleIntersectWith(Schedule $other): bool
    {
        return $this->schedule->intersectWith($other);
    }
    
    public function belongsToParticipant(Participant $participant): bool
    {
        return $this->participant === $participant;
    }
    
    public function processReportInMentoring(
            NegotiatedMentoring $negotiatedMentoring, FormRecordData $formRecordData, int $mentorRating): void
    {
        if (!$this->schedule->isAlreadyPassed()) {
            throw RegularException::forbidden('forbidden: can only process report on past mentoring');
        }
        $this->consultationSetup->processReportIn($negotiatedMentoring, $formRecordData, $mentorRating);
    }

}
