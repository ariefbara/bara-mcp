<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Participant\Application\Service\RegistrantProfileRepository;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use Resources\Exception\RegularException;

class DoctrineRegistrantProfileRepository extends EntityRepository implements RegistrantProfileRepository
{

    public function aRegistrantProfileCorrespondWithProgramsProfileForm(
            string $programRegistrationId, string $programsProfileFormId): RegistrantProfile
    {
        $params = [
            "registrantId" => $programRegistrationId,
            "programsProfileFormId" => $programsProfileFormId,
        ];
        
        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->andWhere($qb->expr()->eq("registrant.id", ":registrantId"))
                ->leftJoin("registrantProfile.programsProfileForm", "programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ":programsProfileFormId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: registrant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
