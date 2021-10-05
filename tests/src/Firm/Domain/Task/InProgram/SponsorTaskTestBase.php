<?php

namespace Tests\src\Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program\Sponsor;
use Firm\Domain\Model\Firm\Program\SponsorData;
use Firm\Domain\Task\Dependency\Firm\FirmFileInfoRepository;
use Firm\Domain\Task\Dependency\Firm\SponsorRepository;
use Firm\Domain\Task\InProgram\SponsorRequest;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\src\Firm\Domain\Task\InProgram\TaskInProgramTestBase;

class SponsorTaskTestBase extends TaskInProgramTestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $sponsorRepository;
    /**
     * 
     * @var MockObject
     */
    protected $sponsor;
    /**
     * 
     * @var MockObject
     */
    protected $firmFileInfoRepository;
    protected $firmFileInfo;
    protected $firmFileInfoId = 'firm-file-info-id';
    protected $sponsorId = 'sponsor-id';
    protected $sponsorData;
    protected $sponsorRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->sponsor = $this->buildMockOfClass(Sponsor::class);
        $this->sponsorRepository = $this->buildMockOfInterface(SponsorRepository::class);
        $this->sponsorRepository->expects($this->any())
                ->method('ofId')
                ->with($this->sponsorId)
                ->willReturn($this->sponsor);
        $this->sponsorRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->sponsorId);
        
        $this->firmFileInfo = $this->buildMockOfClass(FirmFileInfo::class);
        $this->firmFileInfoRepository = $this->buildMockOfInterface(FirmFileInfoRepository::class);
        $this->firmFileInfoRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmFileInfoId)
                ->willReturn($this->firmFileInfo);
        
        $this->sponsorRequest = new SponsorRequest('sponsor name', 'http://website.sponsor.id', $this->firmFileInfoId);
        $this->sponsorData = new SponsorData(
                $this->sponsorRequest->getName(), $this->firmFileInfo, $this->sponsorRequest->getWebsite());
    }
}
