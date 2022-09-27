<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest\NegotiatedMentoringRepository;

class ViewNegotiatedMentoringDetail implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var NegotiatedMentoringRepository
     */
    protected $negotiatedMentoringRepository;

    public function __construct(NegotiatedMentoringRepository $negotiatedMentoringRepository)
    {
        $this->negotiatedMentoringRepository = $negotiatedMentoringRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->negotiatedMentoringRepository
                ->aNegotiatedMentoringInProgram($programId, $payload->getId());
    }

}
