<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;

class ViewDeclaredMentoringDetail implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var DeclaredMentoringRepository
     */
    protected $declaredMentoringRepository;

    public function __construct(DeclaredMentoringRepository $declaredMentoringRepository)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->declaredMentoringRepository->aDeclaredMentoringInProgram($programId, $payload->getId());
    }

}
