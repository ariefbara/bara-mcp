<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\Model\Participant;
use SharedContext\Domain\Model\Mentoring;

class BookedMentoringSlot
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
    
    public function cancel(): void
    {
        $this->mentoringSlot->assertCancelBookingAllowed();
        $this->cancelled = true;
    }


}
