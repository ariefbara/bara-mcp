<?php

namespace Personnel\Domain\Service;

use Tests\TestBase;

class PersonnelFileInfoFinderTest extends TestBase
{
    protected $finder;
    protected $fileInfoRepository;
    protected $firmId = 'firmId', $personnelId = 'personnelId';
    
    protected $fileInfoId = 'fileInfoId';

    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        $this->finder = new PersonnelFileInfoFinder($this->fileInfoRepository, $this->firmId, $this->personnelId);
    }
    
    public function test_ofId_returnFileInfoFromRepository()
    {
        $this->fileInfoRepository->expects($this->once())
                ->method('aFileInfoOfPersonnel')
                ->with($this->firmId, $this->personnelId, $this->fileInfoId);
        $this->finder->ofId($this->fileInfoId);
    }
}
