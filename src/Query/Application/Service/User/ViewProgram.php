<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program;

class ViewProgram
{

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    public function showAll(int $page, int $pageSize)
    {
        return $this->programRepository->allProgramForUser($page, $pageSize);
    }

    public function showById(string $firmId, string $programId): Program
    {
        return $this->programRepository->ofId($firmId, $programId);
    }

}
