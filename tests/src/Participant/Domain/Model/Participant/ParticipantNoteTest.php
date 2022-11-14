<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\Participant;
use SharedContext\Domain\Model\Note;
use Tests\TestBase;

class ParticipantNoteTest extends TestBase
{
    protected $participant;
    protected $participantNote, $note;
    protected $id = 'newId', $content = 'new content';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->participantNote = new TestableParticipantNote($this->participant, 'id', 'content');
        $this->note = $this->buildMockOfClass(Note::class);
        $this->participantNote->note = $this->note;
    }
    
    protected function construct()
    {
        return new TestableParticipantNote($this->participant, $this->id, $this->content);
    }
    public function test_construct_setProperties()
    {
        $participantNote = $this->construct();
        $this->assertSame($this->participant, $participantNote->participant);
        $this->assertSame($this->id, $participantNote->id);
        $this->assertInstanceOf(Note::class, $participantNote->note);
    }
    
    //
    protected function update()
    {
        $this->participantNote->update($this->content);
    }
    public function test_update_updateNote()
    {
        $this->note->expects($this->once())
                ->method('update')
                ->with($this->content);
        $this->update();
    }
    
    //
    protected function remove()
    {
        $this->participantNote->remove();
    }
    public function test_remove_removeNOte()
    {
        $this->note->expects($this->once())
                ->method('remove');
        $this->remove();
    }
    
    //
    protected function assertManageableByParticipant()
    {
        $this->participantNote->assertManageableByParticipant($this->participant);
    }
    public function test_assertManageableByParticipant_differentParticipant_forbidden()
    {
        $this->participantNote->participant = $this->buildMockOfClass(Participant::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertManageableByParticipant();
        }, 'Forbidden', 'unmanaged participant note, can only manage owned note');
    }
    public function test_assertManageableByParticipant_sameParticipant_void()
    {
        $this->assertManageableByParticipant();
        $this->markAsSuccess();
    }
}

class TestableParticipantNote extends ParticipantNote{
    public $participant;
    public $id;
    public $note;
}