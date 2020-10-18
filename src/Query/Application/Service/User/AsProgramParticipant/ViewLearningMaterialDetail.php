<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\ {
    Application\Service\User\ProgramParticipationRepository,
    Domain\Model\Firm\Program\Mission\LearningMaterial,
    Domain\Service\LearningMaterialFinder
};
use Resources\Application\Event\Dispatcher;

class ViewLearningMaterialDetail
{

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $userProgramParticipationRepository;

    /**
     *
     * @var LearningMaterialFinder
     */
    protected $learningMaterialFinder;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(
            ProgramParticipationRepository $userProgramParticipationRepository,
            LearningMaterialFinder $learningMaterialFinder, Dispatcher $dispatcher)
    {
        $this->userProgramParticipationRepository = $userProgramParticipationRepository;
        $this->learningMaterialFinder = $learningMaterialFinder;
        $this->dispatcher = $dispatcher;
    }
    
    public function execute(string $userId, string $programId, string $learningMaterialId): LearningMaterial
    {
        $userProgramParticipation = $this->userProgramParticipationRepository
                ->aProgramParticipationOfUserCorrespondWithProgram($userId, $programId);
        $learningMaterial = $userProgramParticipation
                ->viewLearningMaterial($this->learningMaterialFinder, $learningMaterialId);
        
        $this->dispatcher->dispatch($userProgramParticipation);
        return $learningMaterial;
    }

}
