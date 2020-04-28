<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\Client\ {
    ProgramParticipation,
    ProgramParticipation\Worksheet\Comment
};
use Tests\TestBase;

class ParticipantCommentTest extends TestBase
{
    protected $participantComment;
    protected $programParticipation;
    protected $comment;
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->participantComment = new TestableParticipantComment($this->programParticipation, 'id', $this->comment);
    }
    
    public function test_construct_setProperties()
    {
        $participantComment = new TestableParticipantComment($this->programParticipation, $this->id, $this->comment);
        $this->assertEquals($this->programParticipation, $participantComment->programParticipation);
        $this->assertEquals($this->id, $participantComment->id);
        $this->assertEquals($this->comment, $participantComment->comment);
    }
    public function test_remove_removeComment()
    {
        $this->comment->expects($this->once())
                ->method('remove');
        $this->participantComment->remove();
    }
    
}

class TestableParticipantComment extends ParticipantComment
{
    public $programParticipation;
    public $id;
    public $comment;
}
