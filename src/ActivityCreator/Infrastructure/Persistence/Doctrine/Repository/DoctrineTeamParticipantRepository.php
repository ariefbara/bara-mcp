<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\ {
    Application\Service\TeamMember\TeamParticipantRepository,
    Domain\DependencyModel\Firm\Team\ProgramParticipation
};
use Doctrine\ORM\EntityRepository;
use Resources\Exception\RegularException;

class DoctrineTeamParticipantRepository extends EntityRepository implements TeamParticipantRepository
{
    
    public function ofId(string $programParticipationId): ProgramParticipation
    {
        $programParticipation = $this->findOneBy(["id" => $programParticipationId]);
        if (empty($programParticipation)) {
            $errorDetail = "not found: program participation not found";
            throw RegularException::notFound($errorDetail);
        }
        return $programParticipation;
    }

}
