<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\ActivityTypeRepository;
use Query\Domain\Model\Firm\Program\ActivityType;
use Query\Domain\Model\Firm\Program\ActivityType\ActivityParticipant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineActivityTypeRepository extends EntityRepository implements ActivityTypeRepository
{

    public function allActivityTypesInProgram(
            string $programId, int $page, int $pageSize, ?bool $enabledOnly, ?string $userRoleAllowedToInitiate)
    {
        $params = [
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("activityType");
        $qb->select("activityType")
                ->leftJoin("activityType.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);

        if ($enabledOnly) {
            $qb->andWhere($qb->expr()->eq("activityType.disabled", "false"));
        }
        if (isset($userRoleAllowedToInitiate)) {
            $activityParticipantQb = $this->getEntityManager()->createQueryBuilder();
            $activityParticipantQb->select("a_activityType.id")
                    ->from(ActivityParticipant::class, "activityParticipant")
                    ->andWhere($activityParticipantQb->expr()->eq("activityParticipant.participantType.participantType", ":participantType"))
                    ->andWhere($activityParticipantQb->expr()->eq("activityParticipant.participantPriviledge.canInitiate", "true"))
                    ->leftJoin("activityParticipant.activityType", "a_activityType");
            $qb->andWhere($qb->expr()->in("activityType", $activityParticipantQb->getDQL()))
                    ->setParameter("participantType", $userRoleAllowedToInitiate);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActivityTypeInProgram(string $programId, string $activityTypeId): ActivityType
    {
        $params = [
            "programId" => $programId,
            "activityTypeId" => $activityTypeId,
        ];

        $qb = $this->createQueryBuilder("activityType");
        $qb->select("activityType")
                ->andWhere($qb->expr()->eq("activityType.id", ":activityTypeId"))
                ->leftJoin("activityType.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity type not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
