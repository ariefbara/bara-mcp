<?php

namespace Query\Application\Service\Firm\Client\AsProgramParticipant;

use Query\Domain\ {
    Model\Firm\Program\Mission\LearningMaterial,
    Service\LearningMaterialFinder
};
use Resources\Application\Event\Dispatcher;

class ViewLearningMaterialDetail
{

    /**
     *
     * @var ClientProgramParticipationRepository
     */
    protected $clientProgramParticipationRepository;

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

    public function __construct(ClientProgramParticipationRepository $clientProgramParticipationRepository,
            LearningMaterialFinder $learningMaterialFinder, Dispatcher $dispatcher)
    {
        $this->clientProgramParticipationRepository = $clientProgramParticipationRepository;
        $this->learningMaterialFinder = $learningMaterialFinder;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $clientId, string $programId, string $learningMaterialId): LearningMaterial
    {
        $clientProgramParticipation = $this->clientProgramParticipationRepository
                ->aClientProgramParticipationCorrespondWithProgram($clientId, $programId);
        $learningMaterial = $clientProgramParticipation
                ->viewLearningMaterial($this->learningMaterialFinder, $learningMaterialId);
        
        $this->dispatcher->dispatch($clientProgramParticipation);
        return $learningMaterial;
    }

}
