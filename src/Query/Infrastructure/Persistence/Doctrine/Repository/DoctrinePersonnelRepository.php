<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Auth\Firm\PersonnelRepository as InterfaceForAuthorization,
    Application\Service\Firm\PersonnelRepository,
    Domain\Model\Firm\Personnel
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrinePersonnelRepository extends EntityRepository implements PersonnelRepository, InterfaceForAuthorization
{

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

    public function all(string $firmId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->andWhere($qb->expr()->eq('personnel.active', 'true'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofEmail(string $firmIdentifier, string $email): Personnel
    {
        $parameters = [
            "email" => $email,
            "firmIdentifier" => $firmIdentifier,
        ];

        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->andWhere($qb->expr()->eq('personnel.active', 'true'))
                ->andWhere($qb->expr()->eq('personnel.email', ':email'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.identifier', ':firmIdentifier'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function containRecordOfActivePersonnelInFirm(string $firmId, string $personnelId): bool
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
        ];
        
        $qb = $this->createQueryBuilder("personnel");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->andWhere($qb->expr()->eq("personnel.active", "true"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        return !empty($qb->getQuery()->getResult());
    }

}
