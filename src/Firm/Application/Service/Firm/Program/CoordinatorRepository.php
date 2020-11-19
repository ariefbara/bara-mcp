<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\{
    Application\Service\Personnel\ProgramCoordinator\ProgramCoordinatorRepository as InterfaceForPersonnel,
    Domain\Model\Firm\Program\Coordinator
};

interface CoordinatorRepository extends InterfaceForPersonnel
{

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $coordinatorId): Coordinator;

    public function aCoordinatorCorrespondWithPersonnel(string $programId, string $personnelId): Coordinator;

    public function aCoordinatorOfId(string $coordinatorId): Coordinator;
}
