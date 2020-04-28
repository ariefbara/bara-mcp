<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ProgramRegistrationRepository,
    Domain\Model\Client\ProgramRegistration
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder,
    Uuid
};

class DoctrineProgramRegistrationRepository extends EntityRepository implements ProgramRegistrationRepository
{

    public function ofId(string $clientId, string $programRegistrationId): ProgramRegistration
    {
        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
            ->andWhere($qb->expr()->eq('programRegistration.id', ':programRegistrationId'))
            ->setParameter('programRegistrationId', $programRegistrationId)
            ->leftJoin('programRegistration.client', 'client')
            ->andWhere($qb->expr()->eq('client.id', ':clientId'))
            ->setParameter('clientId', $clientId)
            ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: programRegistration not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function add(ProgramRegistration $programRegistration): void
    {
        $em = $this->getEntityManager();
        $em->persist($programRegistration);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function all(string $clientId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
            ->leftJoin('programRegistration.client', 'client')
            ->andWhere($qb->expr()->eq('client.id', ':clientId'))
            ->setParameter('clientId', $clientId);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
