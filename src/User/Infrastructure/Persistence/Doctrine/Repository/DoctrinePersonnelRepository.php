<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;
use User\{
    Application\Service\Personnel\PersonnelRepository,
    Domain\Model\Personnel
};

class DoctrinePersonnelRepository extends EntityRepository implements PersonnelRepository
{

    public function aPersonnelInFirmByEmailAndIdentifier(string $firmIdentifier, string $email): Personnel
    {
        $params = [
            "firmIdentifier" => $firmIdentifier,
            "email" => $email,
        ];

        $qb = $this->createQueryBuilder("personnel");
        $qb->select("personnel")
                ->andWhere($qb->expr()->eq("personnel.email", ":email"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.identifier", ":firmIdentifier"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
