<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\Model\Participant;
use SharedContext\Domain\Model\Mentoring;
use Tests\TestBase;

class BookedMentoringSlotTest extends TestBase
{
    protected $participant;
    protected $mentoringSlot;
    protected $bookedMentoringSlot, $mentoring;
    
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->mentoringSlot = $this->buildMockOfClass(MentoringSlot::class);
        $this->bookedMentoringSlot = new TestableBookedMentoringSlot($this->participant, 'id', $this->mentoringSlot);
        
        $this->mentoring = $this->buildMockOfClass(Mentoring::class);
        $this->bookedMentoringSlot->mentoring = $this->mentoring;
    }
    
    protected function construct()
    {
        return new TestableBookedMentoringSlot($this->participant, $this->id, $this->mentoringSlot);
    }
    public function test_construct_setProperties()
    {
        $bookedMentoringSlot = $this->construct();
        $this->assertSame($this->participant, $bookedMentoringSlot->participant);
        $this->assertSame($this->id, $bookedMentoringSlot->id);
        $this->assertSame($this->mentoringSlot, $bookedMentoringSlot->mentoringSlot);
        $this->assertFalse($bookedMentoringSlot->cancelled);
        $this->assertInstanceOf(Mentoring::class, $bookedMentoringSlot->mentoring);
    }
    
    protected function isActive()
    {
        return $this->bookedMentoringSlot->isActive();
    }
    public function test_isActive_activeBooking_returnTrue()
    {
        $this->assertTrue($this->isActive());
    }
    public function test_isActive_cancelledBooking_returnFalse()
    {
        $this->bookedMentoringSlot->cancelled = true;
        $this->assertFalse($this->isActive());
    }
    
    protected function isActiveBookingCorrespondWithParticipant()
    {
        return $this->bookedMentoringSlot->isActiveBookingCorrespondWithParticipant($this->participant);
    }
    public function test_isActiveBookingCorrespondWithParticipant_activeBookingOfSameParticipant_returnTrue()
    {
        $this->assertTrue($this->isActiveBookingCorrespondWithParticipant());
    }
    public function test_isActiveBookingCorrespondWithParticipant_cancelledBooking_returnFalse()
    {
        $this->bookedMentoringSlot->cancelled = true;
        $this->assertFalse($this->isActiveBookingCorrespondWithParticipant());
    }
    public function test_isActiveBookingCorrespondWithParticipant_differentParticipant_returnFalse()
    {
        $this->bookedMentoringSlot->participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->isActiveBookingCorrespondWithParticipant());
    }
    
    protected function belongsToParticipant()
    {
        return $this->bookedMentoringSlot->belongsToParticipant($this->participant);
    }
    public function test_belongsToParticipant_sameParticipant_returnTrue()
    {
        $this->assertTrue($this->belongsToParticipant());
    }
    public function test_belongsToParticipant_differentParticipant_returnFalse()
    {
        $this->bookedMentoringSlot->participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->belongsToParticipant());
    }
    
    protected function cancel()
    {
        $this->bookedMentoringSlot->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->cancel();
        $this->assertTrue($this->bookedMentoringSlot->cancelled);
    }
    public function test_cancel_assertMentoringSlotAllowBookingCancel()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('assertCancelBookingAllowed');
        $this->cancel();
    }
}

class TestableBookedMentoringSlot extends BookedMentoringSlot
{
    public $participant;
    public $id;
    public $cancelled;
    public $mentoringSlot;
    public $mentoring;
}
