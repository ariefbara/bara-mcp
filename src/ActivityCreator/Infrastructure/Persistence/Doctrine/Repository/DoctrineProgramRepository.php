<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\ {
    Application\Service\Manager\ProgramRepository,
    Domain\DependencyModel\Firm\Program
};
use Doctrine\ORM\EntityRepository;
use Resources\Exception\RegularException;

class DoctrineProgramRepository extends EntityRepository implements ProgramRepository
{
    
    public function ofId(string $programId): Program
    {
        $program = $this->findOneBy(["id" => $programId]);
        if (empty($program)) {
            $errorDetail = "not found: program not found";
            throw RegularException::notFound($errorDetail);
        }
        return $program;
    }

}
