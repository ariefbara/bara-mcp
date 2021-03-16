<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet;

use Participant\Application\Service\Firm\Client\TeamMembershipRepository;
use Participant\Application\Service\Participant\WorksheetRepository;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\Participant\Worksheet;
use Tests\TestBase;

class SubmitNewCommentTest extends TestBase
{

    protected $service;
    protected $memberCommentRepository, $nextId = "nextId";
    protected $worksheetRepository, $worksheet;
    protected $teamMembershipRepository, $teamMembership;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $teamProgramParticipationId = "teamProgramParticipationId", $worksheetId = "worksheetId", $message = "message";

    protected function setUp(): void
    {
        parent::setUp();
        $this->memberCommentRepository = $this->buildMockOfInterface(MemberCommentRepository::class);
        $this->memberCommentRepository->expects($this->any())->method("nextIdentity")->willReturn($this->nextId);

        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method("aWorksheetBelongsToTeamParticipant")
                ->with($this->teamProgramParticipationId, $this->worksheetId)
                ->willReturn($this->worksheet);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfClass(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);

        $this->service = new SubmitNewComment(
                $this->memberCommentRepository, $this->worksheetRepository, $this->teamMembershipRepository);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->teamMembershipId, $this->teamProgramParticipationId,
                        $this->worksheetId, $this->message);
    }
    public function test_execute_addCommentToRepository()
    {
        $this->teamMembership->expects($this->once())
                ->method("submitNewCommentInWorksheet")
                ->with($this->worksheet, $this->nextId, $this->message);
        $this->memberCommentRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
