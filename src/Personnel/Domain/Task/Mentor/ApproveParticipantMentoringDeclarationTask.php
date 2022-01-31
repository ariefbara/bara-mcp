<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\DeclaredMentoringRepository;

class ApproveParticipantMentoringDeclarationTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var DeclaredMentoringRepository
     */
    protected $declaredMentoringRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    public function __construct(DeclaredMentoringRepository $declaredMentoringRepository, string $id)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
        $this->id = $id;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $declaredMentoring = $this->declaredMentoringRepository->ofId($this->id);
        $declaredMentoring->assertManageableByMentor($mentor);
        $declaredMentoring->approveParticipantDeclaration();
    }

}
