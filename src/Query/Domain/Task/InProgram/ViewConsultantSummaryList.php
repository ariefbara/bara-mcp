<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\ConsultantRepository;

class ViewConsultantSummaryList implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    public function __construct(ConsultantRepository $consultantRepository)
    {
        $this->consultantRepository = $consultantRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->consultantRepository->consultantSummaryListInProgram($programId);
    }

}
