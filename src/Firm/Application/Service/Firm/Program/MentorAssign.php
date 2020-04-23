<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\PersonnelRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program\Mentor
};

class MentorAssign
{

    protected $programRepository;
    protected $personnelRepository;

    function __construct(ProgramRepository $programRepository, PersonnelRepository $personnelRepository)
    {
        $this->programRepository = $programRepository;
        $this->personnelRepository = $personnelRepository;
    }

    public function execute(string $firmId, string $programId, string $personnelId): Mentor
    {
        
        $personnel = $this->personnelRepository->ofId($firmId, $personnelId);
        $mentor = $this->programRepository->ofId($firmId, $programId)
                ->assignPersonnelAsMentor($personnel);
        $this->programRepository->update();
        return $mentor;
    }

}
