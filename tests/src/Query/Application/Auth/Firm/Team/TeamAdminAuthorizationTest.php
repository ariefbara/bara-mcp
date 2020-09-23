<?php

namespace Query\Application\Auth\Firm\Team;

use Tests\TestBase;

class TeamAdminAuthorizationTest extends TestBase
{
    protected $authZ;
    protected $memberRepository;
    protected $firmId = "firmId", $teamId = "teamId", $clientId = "clientId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->memberRepository = $this->buildMockOfInterface(MemberRepository::class);
        $this->authZ = new TeamAdminAuthorization($this->memberRepository);
    }
    
    protected function execute()
    {
        $this->memberRepository->expects($this->any())
                ->method("containRecordOfActiveTeamMemberWithAdminPriviledgeCorrespondWithClient")
                ->willReturn(true);
        $this->authZ->execute($this->firmId, $this->teamId, $this->clientId);
    }
    public function test_execute_containMemberWithAdminPriviledgeCorrespondWithClient_void()
    {
        $this->execute();
        $this->markAsSuccess();
    }
    public function test_execute_noRecordOfMemberHavingAdminPriviledgeCorrespondWithClient_forbiddenError()
    {
        $this->memberRepository->expects($this->once())
                ->method("containRecordOfActiveTeamMemberWithAdminPriviledgeCorrespondWithClient")
                ->with($this->firmId, $this->teamId, $this->clientId)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only team member with admin priviledge can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}
