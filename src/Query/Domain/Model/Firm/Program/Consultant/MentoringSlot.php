<?php

namespace Query\Domain\Model\Firm\Program\Consultant;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use SharedContext\Domain\ValueObject\Schedule;

class MentoringSlot
{

    /**
     * 
     * @var Consultant
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

    public function getMentor(): Consultant
    {
        return $this->mentor;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCancelled(): bool
    {
        return $this->cancelled;
    }


    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getConsultationSetup(): ConsultationSetup
    {
        return $this->consultationSetup;
    }


    protected function __construct()
    {
        
    }
    
    public function getStartTimeString(): string
    {
        return $this->schedule->getStartTimeString();
    }

    public function getEndTimeString(): string
    {
        return $this->schedule->getEndTimeString();
    }

    public function getMediaType(): ?string
    {
        return $this->schedule->getMediaType();
    }

    public function getLocation(): ?string
    {
        return $this->schedule->getLocation();
    }
    
    /**
     * 
     * @return BookedMentoringSlot[]
     */
    public function iterateActiveBookedSlots()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('cancelled', false));
        return $this->bookedSlots->matching($criteria)->getIterator();
    }
}
