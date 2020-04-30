<?php

namespace Personnel\Domain\Service;

use Personnel\Application\Service\Firm\Personnel\PersonnelCompositionId;
use Shared\Domain\Model\FileInfo;
use Tests\TestBase;

class PersonnelFileInfoFinderTest extends TestBase
{
    protected $finder;
    protected $personnelFileInfoRepository;
    
    protected $personnelCompositionId, $personnelFileInfoId = 'personnelFileInfoId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelFileInfoRepository = $this->buildMockOfInterface(PersonnelFileInfoRepository::class);
        $this->personnelCompositionId = $this->buildMockOfClass(PersonnelCompositionId::class);
        $this->finder = new PersonnelFileInfoFinder($this->personnelFileInfoRepository, $this->personnelCompositionId);
    }
    
    public function test_ofId_returnFileInfoFromRepository()
    {
        $this->personnelFileInfoRepository->expects($this->once())
                ->method('fileInfoOf')
                ->with($this->personnelCompositionId, $this->personnelFileInfoId)
                ->willReturn($fileInfo = $this->buildMockOfClass(FileInfo::class));
        $this->assertEquals($fileInfo, $this->finder->ofId($this->personnelFileInfoId));
    }
}
