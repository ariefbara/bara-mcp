<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet;

use Participant\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\Worksheet\CommentRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\Worksheet\Comment
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ReplyCommentTest extends TestBase
{

    protected $commentRepository, $comment, $nextId = "nextId";
    protected $teamMembershipRepository, $teamMembership;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId";
    protected $teamProgramParticipationId = "teamProgramParticipationId", $worksheetId = "worksheetId", $commentId = "commentId";
    protected $message = "message";

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->commentRepository->expects($this->any())->method("nextIdentity")->willReturn($this->nextId);
        $this->commentRepository->expects($this->any())
                ->method("aCommentBelongsToTeamParticipant")
                ->with($this->teamProgramParticipationId, $this->worksheetId, $this->commentId)
                ->willReturn($this->comment);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfClass(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ReplyComment($this->commentRepository, $this->teamMembershipRepository, $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->teamMembershipId, $this->teamProgramParticipationId,
                $this->worksheetId, $this->commentId, $this->message);
    }
    public function test_execute_addReplyToRepository()
    {
        $reply = $this->buildMockOfClass(Comment::class);
        $this->teamMembership->expects($this->once())
                ->method("replyComment")
                ->with($this->comment, $this->nextId, $this->message)
                ->willReturn($reply);
        $this->commentRepository->expects($this->once())
                ->method("add")
                ->with($reply);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatchReply()
    {
        $reply = $this->buildMockOfClass(Comment::class);
        $this->teamMembership->expects($this->once())
                ->method("replyComment")
                ->willReturn($reply);
        
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($reply);
        
        $this->execute();
    }

}
