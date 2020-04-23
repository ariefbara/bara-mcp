<?php

namespace Firm\Application\Service\Firm\Program;

class MentorRemove
{
    protected $mentorRepository;
    
    function __construct(MentorRepository $mentorRepository)
    {
        $this->mentorRepository = $mentorRepository;
    }
    
    public function execute(ProgramCompositionId $programCompositionId, $mentorId): void
    {
        $this->mentorRepository->ofId($programCompositionId, $mentorId)
            ->remove();
        $this->mentorRepository->update();
    }

}
