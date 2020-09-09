<?php

namespace Personnel\Application\Service\Firm\Personnel;

use Personnel\Application\Service\Firm\PersonnelRepository;
use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;

class PersonnelFileUploadTest extends TestBase
{
    protected $service;
    protected $personnelFileInfoRepository;
    protected $personnelRepository;
    protected $uploadFile;
    
    protected $firmId = 'firm-id', $personnelId = 'personnel-id';
    protected $fileInfoData, $contents = 'string represent stream resource';

    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelFileInfoRepository = $this->buildMockOfInterface(PersonnelFileInfoRepository::class);
        $this->personnelRepository = $this->buildMockOfClass(PersonnelRepository::class);
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
        
        $this->service = new PersonnelFileUpload($this->personnelFileInfoRepository, $this->personnelRepository, $this->uploadFile);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())
            ->method('getName')
            ->willReturn('filename.jpg');
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->fileInfoData, $this->contents);
    }
    
    public function test_execute_addPersonnelFileInfoToRepository()
    {
        $this->personnelFileInfoRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }
    public function test_execute_executeUploadFile()
    {
        $this->uploadFile->expects($this->once())
            ->method('execute');
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->personnelFileInfoRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
    }
}
