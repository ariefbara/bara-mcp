<?php

namespace Personnel\Domain\Model\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Model\Note;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class CoordinatorNoteTest extends TestBase
{
    protected $coordinator;
    protected $participant;
    protected $coordinatorNote, $note;
    //
    protected $id = 'newId', $labelData, $viewableByParticipant = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->coordinatorNote = new TestableCoordinatorNote($this->coordinator, $this->participant, 'id', new LabelData('name', 'description'), false);
        $this->note = $this->buildMockOfClass(Note::class);
        $this->coordinatorNote->note = $this->note;
        
        $this->labelData = new LabelData('new nambe', 'new description');
    }
    
    //
    protected function construct()
    {
        return new TestableCoordinatorNote($this->coordinator, $this->participant, $this->id, $this->labelData, $this->viewableByParticipant);
    }
    public function test_construct_setProperties()
    {
        $coordinatorNote = $this->construct();
        $this->assertSame($this->coordinator, $coordinatorNote->coordinator);
        $this->assertSame($this->participant, $coordinatorNote->participant);
        $this->assertSame($this->id, $coordinatorNote->id);
        $this->assertSame($this->viewableByParticipant, $coordinatorNote->viewableByParticipant);
        $this->assertInstanceOf(Note::class, $coordinatorNote->note);
    }
    
    //
    protected function update()
    {
        $this->coordinatorNote->update($this->labelData);
    }
    public function test_update_updateNote()
    {
        $this->note->expects($this->once())
                ->method('update')
                ->with($this->labelData);
        $this->update();
    }
    
    //
    protected function showToParticipant()
    {
        $this->coordinatorNote->showToParticipant();
    }
    public function test_showToParticipant_setViewableByParticipant()
    {
        $this->showToParticipant();
        $this->assertTrue($this->coordinatorNote->viewableByParticipant);
    }
    
    //
    protected function hideFromParticipant()
    {
        $this->coordinatorNote->HideFromParticipant();
    }
    public function test_hideFromParticipant_setHiddenFromParticipant()
    {
        $this->coordinatorNote->viewableByParticipant = true;
        $this->hideFromParticipant();
        $this->assertFalse($this->coordinatorNote->viewableByParticipant);
    }
    
    //
    protected function remove()
    {
        $this->coordinatorNote->remove();
    }
    public function test_remove_removeNote()
    {
        $this->note->expects($this->once())
                ->method('remove');
        $this->remove();
    }
    
    //
    protected function assertManageableByCoordinator()
    {
        $this->coordinatorNote->assertManageableByCoordinator($this->coordinator);
    }
    public function test_assertManageableByCoordinator_differentCoordinator_forbidden()
    {
        $this->coordinatorNote->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertManageableByCoordinator();
        }, 'Forbidden', 'unmanaged coordinator note, can only managed own note');
    }
    public function test_assertManageableByCoordinator_sameCoordinator_void()
    {
        $this->assertManageableByCoordinator();
        $this->markAsSuccess();
    }
}

class TestableCoordinatorNote extends CoordinatorNote
{
    public $coordinator;
    public $participant;
    public $id;
    public $note;
    public $viewableByParticipant;
}
