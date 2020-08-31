<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Uuid
};
use User\ {
    Application\Service\User\ProgramRegistrationRepository,
    Domain\Model\User\ProgramRegistration
};

class DoctrineProgramRegistrationRepository extends EntityRepository implements ProgramRegistrationRepository
{

    public function ofId(string $userId, string $programRegistrationId): ProgramRegistration
    {
        $params = [
            'userId' => $userId,
            'programRegistrationId' => $programRegistrationId,
        ];
        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->andWhere($qb->expr()->eq('programRegistration.id', ':programRegistrationId'))
                ->leftJoin('programRegistration.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
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
