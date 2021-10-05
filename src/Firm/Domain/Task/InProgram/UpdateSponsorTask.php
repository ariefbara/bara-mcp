<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\ITaskInProgramExecutableByManager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\SponsorData;
use Firm\Domain\Task\Dependency\Firm\FirmFileInfoRepository;
use Firm\Domain\Task\Dependency\Firm\SponsorRepository;
use Firm\Domain\Task\InProgram\SponsorRequest;

class UpdateSponsorTask implements ITaskInProgramExecutableByManager
{

    /**
     * 
     * @var SponsorRepository
     */
    protected $sponsorRepository;

    /**
     * 
     * @var FirmFileInfoRepository
     */
    protected $firmFileInfoRepository;

    /**
     * 
     * @var string
     */
    protected $sponsorId;

    /**
     * 
     * @var SponsorRequest
     */
    protected $payload;

    public function __construct(
            SponsorRepository $sponsorRepository, FirmFileInfoRepository $firmFileInfoRepository, string $sponsorId,
            SponsorRequest $payload)
    {
        $this->sponsorRepository = $sponsorRepository;
        $this->firmFileInfoRepository = $firmFileInfoRepository;
        $this->sponsorId = $sponsorId;
        $this->payload = $payload;
    }

    public function executeInProgram(Program $program): void
    {
        $sponsor = $this->sponsorRepository->ofId($this->sponsorId);
        $sponsor->assertManageableInProgram($program);
        
        $logo = empty($this->payload->getFirmFileInfoId()) ? 
                null: $this->firmFileInfoRepository->ofId($this->payload->getFirmFileInfoId());
        $sponsorData = new SponsorData($this->payload->getName(), $logo, $this->payload->getWebsite());
        $sponsor->update($sponsorData);
    }

}
