<?php

namespace Participant\Domain\DependencyModel\Firm\Client\TeamMember;

use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\Participant\Worksheet\Comment;
use Resources\Application\Event\Event;
use Tests\TestBase;

class MemberCommentTest extends TestBase
{
    protected $member;
    protected $comment;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->member = $this->buildMockOfClass(TeamMembership::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableMemberComment($this->member, $this->comment);
    }
    
    public function test_construct_setProperties()
    {
        $memberComment = $this->executeConstruct();
        $this->assertEquals($this->member, $memberComment->member);
        $this->assertEquals($this->comment, $memberComment->comment);
    }
    public function test_construct_pullEventsFromComment()
    {
        $this->comment->expects($this->once())
                ->method('pullRecordedEvents')
                ->willReturn($events = [$this->buildMockOfInterface(Event::class)]);
        $memberComment = $this->executeConstruct();
        $this->assertEquals($events, $memberComment->recordedEvents);
    }
}

class TestableMemberComment extends MemberComment
{
    public $member;
    public $comment;
    public $recordedEvents;
}
