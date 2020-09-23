<?php

namespace Team\Application\Service\Team;

use Team\{
    Application\Service\ClientRepository,
    Domain\DependencyModel\Firm\Client,
    Domain\Model\Team\Member
};
use Tests\TestBase;

class AddMemberTest extends TestBase
{

    protected $service;
    protected $memberRepository, $admin;
    protected $clientRepository, $client;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $clientIdToBeAddedAsMember = "clientIdOfMember";
    protected $anAdmin = true, $memberPosition = "member position";

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->buildMockOfClass(Member::class);
        $this->memberRepository = $this->buildMockOfInterface(MemberRepository::class);
        $this->memberRepository->expects($this->any())
                ->method("aMemberCorrespondWithClient")
                ->with($this->firmId, $this->teamId, $this->clientId)
                ->willReturn($this->admin);

        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientIdToBeAddedAsMember)
                ->willReturn($this->client);

        $this->service = new AddMember($this->memberRepository, $this->clientRepository);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->teamId, $this->clientId, $this->clientIdToBeAddedAsMember, $this->anAdmin,
                        $this->memberPosition);
    }

    public function test_execute_executeAdminsAddTeamMemberMethod()
    {
        $this->admin->expects($this->once())
                ->method("addTeamMember")
                ->with($this->client, $this->anAdmin, $this->memberPosition)
                ->willReturn($memberId = "memberId");
        $this->assertEquals($memberId, $this->execute());
    }
    public function test_updateMemberRepository()
    {
        $this->memberRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
