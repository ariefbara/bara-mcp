<?php

namespace Team\Application\Service\Team;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Team\Domain\Model\Team\ {
    Member,
    TeamFileInfo
};
use Tests\TestBase;

class UploadTeamFileTest extends TestBase
{

    protected $service;
    protected $teamFileInfoRepository, $nextId = "nextId";
    protected $memberRepository, $member;
    protected $uploadFile;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId";
    protected $fileInfoData, $content = "content";

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamFileInfoRepository = $this->buildMockOfInterface(TeamFileInfoRepository::class);
        $this->teamFileInfoRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->member = $this->buildMockOfClass(Member::class);
        $this->memberRepository = $this->buildMockOfInterface(MemberRepository::class);
        $this->memberRepository->expects($this->any())
                ->method("aMemberCorrespondWithClient")
                ->with($this->firmId, $this->teamId, $this->clientId)
                ->willReturn($this->member);

        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);

        $this->service = new UploadTeamFile($this->teamFileInfoRepository, $this->memberRepository,
                $this->uploadFile);

        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->teamId, $this->fileInfoData, $this->content);
    }
    public function test_execute_addTeamFileInfoToRepository()
    {
        $this->member->expects($this->once())
                ->method("uploadFile")
                ->with($this->nextId, $this->fileInfoData);
        $this->teamFileInfoRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_executeTeamFileInfosUploadContent()
    {
        $teamFileInfo = $this->buildMockOfClass(TeamFileInfo::class);
        $this->member->expects($this->once())
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
