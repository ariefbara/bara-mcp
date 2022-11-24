<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Model\Note;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class ConsultantNoteTest extends TestBase
{
    protected $consultant;
    protected $participant;
    protected $consultantNote, $note;
    protected $id = 'newId', $labelData, $viewableByParticipant = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->consultantNote = new TestableConsultantNote($this->consultant, $this->participant, 'id', new LabelData('name', 'description'), false);
        $this->note = $this->buildMockOfClass(Note::class);
        $this->consultantNote->note = $this->note;
        
        $this->labelData = new LabelData('new name', 'new description');
    }
    
    //
    protected function construct()
    {
        return new TestableConsultantNote($this->consultant, $this->participant, $this->id, $this->labelData, $this->viewableByParticipant);
    }
    public function test_construct_setProperties()
    {
        $consultantNote = $this->construct();
        $this->assertSame($this->consultant, $consultantNote->consultant);
        $this->assertSame($this->participant, $consultantNote->participant);
        $this->assertSame($this->id, $consultantNote->id);
        $this->assertInstanceOf(Note::class, $consultantNote->note);
        $this->assertSame($this->viewableByParticipant, $consultantNote->viewableByParticipant);
    }
    
    //
    protected function update()
    {
        $this->consultantNote->update($this->labelData);
    }
    public function test_update_updateNote()
    {
        $this->note->expects($this->once())
                ->method('update')
                ->with($this->labelData);
        $this->update();
    }
    
    protected function showToParticipant()
    {
        $this->consultantNote->showToParticipant();
    }
    public function test_showToParticipant_setViewableByParticipant()
    {
        $this->showToParticipant();
        $this->assertTrue($this->consultantNote->viewableByParticipant);
    }
    
    protected function hideFromParticipant()
    {
        $this->consultantNote->HideFromParticipant();
    }
    public function test_hideFromParticipant_setViewableByParticipantFalse()
    {
        $this->consultantNote->viewableByParticipant = true;
        $this->hideFromParticipant();
        $this->assertFalse($this->consultantNote->viewableByParticipant);
    }
    
    protected function remove()
    {
        $this->consultantNote->remove();
    }
    public function test_remove_removeNote()
    {
        $this->note->expects($this->once())
                ->method('remove');
        $this->remove();
    }
    
    protected function assertManageableByConsultant()
    {
        $this->consultantNote->assertManageableByConsultant($this->consultant);
    }
    public function test_assertManageableByConsultant_differentConsultant_forbidden()
    {
        $this->consultantNote->consultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertManageableByConsultant();
        }, 'Forbidden', 'can only manage own consultant note');
    }
    public function test_assertManageableByConsultant_sameConsultant_void()
    {
        $this->assertManageableByConsultant();
        $this->markAsSuccess();
    }
}

class TestableConsultantNote extends ConsultantNote
{
    public $consultant;
    public $participant;
    public $id;
    public $note;
    public $viewableByParticipant;
}
