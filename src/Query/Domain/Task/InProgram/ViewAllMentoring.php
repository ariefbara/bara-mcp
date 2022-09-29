<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\Dependency\MentoringRepository;

class ViewAllMentoring implements ProgramTaskExecutableByCoordinator, ProgramTaskExecutableByConsultant
{

    /**
     * 
     * @var MentoringRepository
     */
    protected $mentoringRepository;

    public function __construct(MentoringRepository $mentoringRepository)
    {
        $this->mentoringRepository = $mentoringRepository;
    }

    /**
     * 
     * @param string $programId
     * @param ViewAllMentoringPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->mentoringRepository->allValidMentoringsInProgram($programId, $payload->getFilter());
    }

}
