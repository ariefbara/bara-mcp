<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Firm\{
    Application\Service\Firm\PersonnelRepository,
    Application\Service\Manager\PersonnelRepository as InterfaceForManager,
    Domain\Model\Firm\Personnel
};
use Resources\{
    Exception\RegularException,
    Uuid
};

class DoctrinePersonnelRepository extends EntityRepository implements PersonnelRepository, InterfaceForManager
{

    public function add(Personnel $personnel): void
    {
        $em = $this->getEntityManager();
        $em->persist($personnel);
        $em->flush();
    }

    public function isEmailAvailable(string $firmId, string $email): bool
    {
        $qb = $this->createQueryBuilder('personnel');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('personnel.email', ':personnelEmail'))
                ->setParameter('personnelEmail', $email)
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId)
                ->setMaxResults(1);

        return empty($qb->getQuery()->getResult());
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $firmId, string $personnelId): Personnel
    {
        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameter('firmId', $firmId)
                ->setParameter('personnelId', $personnelId)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aPersonnelOfId(string $personnelId): Personnel
    {
        $personnel = $this->findOneBy(["id" => $personnelId]);
        if (empty($personnel)) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
        return $personnel;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
