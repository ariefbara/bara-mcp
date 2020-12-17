<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Participant\Application\Service\User\UserRegistrantRepository;
use Participant\Domain\Model\UserRegistrant;
use Resources\Exception\RegularException;

class DoctrineUserRegistrantRepository extends EntityRepository implements UserRegistrantRepository
{

    public function aUserRegistrant(string $userId, string $programRegistrationId): UserRegistrant
    {
        $params = [
            "userId" => $userId,
            "programRegistrationId" => $programRegistrationId,
        ];

        $qb = $this->createQueryBuilder("userRegistrant");
        $qb->select("userRegistrant")
                ->andWhere($qb->expr()->eq("userRegistrant.id", ":programRegistrationId"))
                ->andWhere($qb->expr()->eq("userRegistrant.userId", ":userId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program registration not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
