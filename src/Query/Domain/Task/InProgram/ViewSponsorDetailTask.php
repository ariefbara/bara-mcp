<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\ITaskInProgramExecutableByManager;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Sponsor;
use Query\Domain\Task\Dependency\Firm\Program\SponsorRepository;

class ViewSponsorDetailTask implements ITaskInProgramExecutableByManager
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
    protected $id;

    /**
     * 
     * @var Sponsor|null
     */
    public $result;

    public function __construct(SponsorRepository $sponsorRepository, string $id)
    {
        $this->sponsorRepository = $sponsorRepository;
        $this->id = $id;
        $this->result = null;
    }

    public function executeInProgram(Program $program): void
    {
        $this->result = $this->sponsorRepository->aSponsorInProgram($program, $this->id);
    }

}
