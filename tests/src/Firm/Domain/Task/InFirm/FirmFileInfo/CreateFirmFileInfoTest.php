<?php

namespace Firm\Domain\Task\InFirm\FirmFileInfo;

use Resources\Application\Event\Dispatcher;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class CreateFirmFileInfoTest extends FirmTaskTestBase
{

    protected $dispatcher;
    protected $task;
    protected $fileInfoData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setFirmFileInfoRelatedDependency();
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        //
        $this->task = new CreateFirmFileInfo($this->firmFileInfoRepository, $this->dispatcher);
        //
        $this->fileInfoData = new FileInfoData('name', null);
    }
    
    //
    protected function execute()
    {
        $this->firmFileInfoRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->firmFileInfoId);
        $this->firm->expects($this->any())
                ->method('createFileInfo')
                ->with($this->firmFileInfoId, $this->fileInfoData)
                ->willReturn($this->firmFileInfo);
        $this->task->executeInFirm($this->firm, $this->fileInfoData);
    }
    public function test_execute_addFileFileInfoCreatedInFirmToRepository()
    {
        $this->firm->expects($this->once())
                ->method('createFileInfo')
                ->with($this->firmFileInfoId, $this->fileInfoData)
                ->willReturn($this->firmFileInfo);
        $this->firmFileInfoRepository->expects($this->once())
                ->method('add')
                ->with($this->firmFileInfo);
        $this->execute();
    }
    public function test_execute_setPayloadId()
    {
        $this->execute();
        $this->assertSame($this->firmFileInfoId, $this->fileInfoData->id);
    }
    public function test_execute_dispatchFirmFileInfo()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->firmFileInfo);
        $this->execute();
    }

}
