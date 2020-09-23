<?php

namespace Team\Application\Service\Team;

use Team\Domain\Model\Team\Member;
use Tests\TestBase;

class RemoveMemberTest extends TestBase
{
    protected $service;
    protected $memberRepository, $admin, $member;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $memberId = "memberId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->buildMockOfClass(Member::class);
        $this->member = $this->buildMockOfClass(Member::class);
        $this->memberRepository = $this->buildMockOfInterface(MemberRepository::class);
        $this->memberRepository->expects($this->any())
                ->method("aMemberCorrespondWithClient")
                ->with($this->firmId, $this->teamId, $this->clientId)
                ->willReturn($this->admin);
        $this->memberRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->teamId, $this->memberId)
                ->willReturn($this->member);
        
        $this->service = new RemoveMember($this->memberRepository);
    }
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->teamId, $this->clientId, $this->memberId);
    }
    public function test_execute_executeAdminsRemoveMemberMethod()
    {
        $this->admin->expects($this->once())
                ->method("removeOtherMember")
                ->with($this->member);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->memberRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
