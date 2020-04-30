<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\ {
    Personnel,
    Program\Participant\Worksheet\Comment
};
use Tests\TestBase;

class PersonnelCommentTest extends TestBase
{
    protected $personnelComment;
    protected $personnel;
    protected $comment;
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->personnelComment = new TestablePersonnelComment($this->personnel, 'id', $this->comment);
    }
    
    public function test_construct_setProperties()
    {
        $personnelComment = new TestablePersonnelComment($this->personnel, $this->id, $this->comment);
        $this->assertEquals($this->personnel, $personnelComment->personnel);
        $this->assertEquals($this->id, $personnelComment->id);
        $this->assertEquals($this->comment, $personnelComment->comment);
    }
    public function test_remove_removeComment()
    {
        $this->comment->expects($this->once())
                ->method('remove');
        $this->personnelComment->remove();
    }
    
}

class TestablePersonnelComment extends PersonnelComment
{
    public $personnel;
    public $id;
    public $comment;
}
