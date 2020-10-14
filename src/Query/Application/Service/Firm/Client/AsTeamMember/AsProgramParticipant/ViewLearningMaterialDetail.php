<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant;

use Query\{
    Application\Service\Firm\Client\AsTeamMember\TeamMemberRepository,
    Application\Service\Firm\Client\AsTeamMember\TeamMembershipRepository,
    Domain\Service\LearningMaterialFinder,
    Domain\Service\TeamProgramParticipationFinder
};
use Resources\Application\Event\Dispatcher;

class ViewLearningMaterialDetail
{

    /**
     *
     * @var TeamMemberRepository
     */
    protected $teamMemberRepositoryRepository;

    /**
     *
     * @var TeamProgramParticipationFinder
     */
    protected $teamProgramParticipationFinder;

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
            TeamMemberRepository $teamMemberRepositoryRepository,
            TeamProgramParticipationFinder $teamProgramParticipationFinder,
            LearningMaterialFinder $learningMaterialFinder, Dispatcher $dispatcher)
    {
        $this->teamMemberRepositoryRepository = $teamMemberRepositoryRepository;
        $this->teamProgramParticipationFinder = $teamProgramParticipationFinder;
        $this->learningMaterialFinder = $learningMaterialFinder;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $clientId, string $teamId, string $programId, string $learningMaterialId)
    {
        $teamMember = $this->teamMemberRepositoryRepository->aTeamMembershipCorrespondWithTeam($clientId, $teamId);
        $learningMaterial = $teamMember->viewLearningMaterial(
                $this->teamProgramParticipationFinder, $programId, $this->learningMaterialFinder, $learningMaterialId);

        $this->dispatcher->dispatch($teamMember);

        return $learningMaterial;
    }

}
