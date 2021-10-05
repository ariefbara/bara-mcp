<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\ITaskInProgramExecutableByManager;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Sponsor;
use Query\Domain\Task\Dependency\Firm\Program\SponsorRepository;

class ViewAllSponsorsTask implements ITaskInProgramExecutableByManager
{
    /**
     * 
     * @var SponsorRepository
     */
    protected $sponsorRepository;
    /**
     * 
     * @var ViewAllSponsorsPayload
     */
    protected $payload;
    
    /**
     * 
     * @var Sponsor[]
     */
    public $result;
    
    public function __construct(SponsorRepository $sponsorRepository, ViewAllSponsorsPayload $payload)
    {
        $this->sponsorRepository = $sponsorRepository;
        $this->payload = $payload;
        $this->result = [];
    }

    
    public function executeInProgram(Program $program): void
    {
        $this->result = $this->sponsorRepository
                ->allSponsorsInProgram(
                        $program, $this->payload->getPage(), $this->payload->getPageSize(), $this->payload->getActiveStatus());
    }

}
