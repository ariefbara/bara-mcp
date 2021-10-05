<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\ITaskInProgramExecutableByManager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\SponsorData;
use Firm\Domain\Task\Dependency\Firm\FirmFileInfoRepository;
use Firm\Domain\Task\Dependency\Firm\SponsorRepository;

class AddSponsorTask implements ITaskInProgramExecutableByManager
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
     * @var SponsorRequest
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $createdSponsorId;

    public function __construct(
            SponsorRepository $sponsorRepository, FirmFileInfoRepository $firmFileInfoRepository,
            SponsorRequest $payload)
    {
        $this->sponsorRepository = $sponsorRepository;
        $this->firmFileInfoRepository = $firmFileInfoRepository;
        $this->payload = $payload;
        $this->createdSponsorId = null;
    }

    public function executeInProgram(Program $program): void
    {
        $logo = empty($this->payload->getFirmFileInfoId()) ? 
                null : $this->firmFileInfoRepository->ofId($this->payload->getFirmFileInfoId());
        $sponsorData = new SponsorData($this->payload->getName(), $logo, $this->payload->getWebsite());
        $this->createdSponsorId = $this->sponsorRepository->nextIdentity();
        
        $sponsor = $program->createSponsor($this->createdSponsorId, $sponsorData);
        $this->sponsorRepository->add($sponsor);
    }

}
