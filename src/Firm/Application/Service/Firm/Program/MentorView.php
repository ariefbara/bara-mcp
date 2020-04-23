<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mentor;

class MentorView
{
    /**
     *
     * @var MentorRepository
     */
    protected $mentorRepository;
    
    function __construct(MentorRepository $mentorRepository)
    {
        $this->mentorRepository = $mentorRepository;
    }
    
    public function showById(ProgramCompositionId $programCompositionId, string $mentorId): Mentor
    {
        return $this->mentorRepository->ofId($programCompositionId, $mentorId);
    }
    
    /**
     * 
     * @param ProgramCompositionId $programCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Mentor[]
     */
    public function showAll(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        return $this->mentorRepository->all($programCompositionId, $page, $pageSize);
    }

}
