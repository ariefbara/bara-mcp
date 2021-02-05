<?php

namespace Query\Application\Service;

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
    
    /**
     * 
     * @param int $page
     * @param int $pageSize
     * @return Program[]
     */
    public function showAll(int $page, int $pageSize)
    {
        return $this->programRepository->allPublishedProgram($page, $pageSize);
    }
    
    public function showById(string $id): Program
    {
        return $this->programRepository->aPublishedProgram($id);
    }

}
