<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\{
    Application\Service\UserParticipant\UserParticipantRepository,
    Domain\DependencyModel\User\ProgramParticipation
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineUserParticipantRepository extends EntityRepository implements UserParticipantRepository
{

    public function aProgramParticipationBelongsToUser(string $userId, string $programParticipationId): ProgramParticipation
    {
        $params = [
            "userId" => $userId,
            "programParticipationId" => $programParticipationId,
        ];

        $qb = $this->createQueryBuilder("programParticipation");
        $qb->select("programParticipation")
                ->andWhere($qb->expr()->eq("programParticipation.id", ":programParticipationId"))
                ->andWhere($qb->expr()->eq("programParticipation.userId", ":userId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
