<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Participant\Application\Service\ProgramsProfileFormRepository;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Resources\Exception\RegularException;

class DoctrineProgramsProfileFormRepository extends EntityRepository implements ProgramsProfileFormRepository
{
    
    public function ofId(string $programsProfileFormId): ProgramsProfileForm
    {
        $programsProfileForm = $this->findOneBy(["id" => $programsProfileFormId]);
        if (empty($programsProfileForm)) {
            $errorDetail = "not found: program's profile form not found";
            throw RegularException::notFound($errorDetail);
        }
        return $programsProfileForm;
    }

}
