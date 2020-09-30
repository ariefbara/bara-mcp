<?php

namespace Team\Application\Service\Firm\Client\TeamMembership;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Team\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Team\Member,
    Domain\Model\Team\TeamFileInfo
};
use Tests\TestBase;

class UploadTeamFileTest extends TestBase
{

    protected $service;
    protected $teamFileInfoRepository, $nextId = "nextId";
    protected $teamMembershipRepository, $teamMembership;
    protected $uploadFile;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId";
    protected $fileInfoData, $content = "content";

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamFileInfoRepository = $this->buildMockOfInterface(TeamFileInfoRepository::class);
        $this->teamFileInfoRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->teamMembership = $this->buildMockOfClass(Member::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("aTeamMembershipOfClient")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);

        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);

        $this->service = new UploadTeamFile($this->teamFileInfoRepository, $this->teamMembershipRepository,
                $this->uploadFile);

        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->teamMembershipId, $this->fileInfoData, $this->content);
    }
    public function test_execute_addTeamFileInfoToRepository()
    {
        $this->teamMembership->expects($this->once())
                ->method("uploadFile")
                ->with($this->nextId, $this->fileInfoData);
        $this->teamFileInfoRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_executeTeamFileInfosUploadContent()
    {
        $teamFileInfo = $this->buildMockOfClass(TeamFileInfo::class);
        $this->teamMembership->expects($this->once())
                ->method("uploadFile")
                ->with($this->nextId, $this->fileInfoData)
                ->willReturn($teamFileInfo);
        $teamFileInfo->expects($this->once())
                ->method("uploadContents")
                ->with($this->uploadFile, $this->content);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
