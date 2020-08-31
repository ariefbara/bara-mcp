<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\{
    Application\Service\Client\ProgramRegistrationRepository,
    Domain\Model\Client\ProgramRegistration
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\{
    Exception\RegularException,
    Uuid
};

class DoctrineProgramRegistrationRepository extends EntityRepository implements ProgramRegistrationRepository
{

    public function ofId(string $firmId, string $clientId, string $programRegistrationId): ProgramRegistration
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programRegistrationId' => $programRegistrationId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->andWhere($qb->expr()->eq('programRegistration.id', ':programRegistrationId'))
                ->leftJoin('programRegistration.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->andWhere($qb->expr()->eq('client.firmId', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program registration not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
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

}
