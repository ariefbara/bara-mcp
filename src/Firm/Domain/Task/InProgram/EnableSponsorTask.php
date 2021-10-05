<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\ITaskInProgramExecutableByManager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Task\Dependency\Firm\SponsorRepository;

class EnableSponsorTask implements ITaskInProgramExecutableByManager
{

    /**
     * 
     * @var SponsorRepository
     */
    protected $sponsorRepository;

    /**
     * 
     * @var string
     */
    protected $sponsorId;

    public function __construct(SponsorRepository $sponsorRepository, string $sponsorId)
    {
        $this->sponsorRepository = $sponsorRepository;
        $this->sponsorId = $sponsorId;
    }

    public function executeInProgram(Program $program): void
    {
        $sponsor = $this->sponsorRepository->ofId($this->sponsorId);
        $sponsor->assertManageableInProgram($program);
        $sponsor->enable();
    }

}
