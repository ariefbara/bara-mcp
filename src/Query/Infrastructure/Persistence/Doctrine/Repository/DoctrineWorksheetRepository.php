<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException,
    QueryBuilder
};
use Query\ {
    Application\Service\Firm\Program\Participant\WorksheetRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant,
    Infrastructure\QueryFilter\WorksheetFilter
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineWorksheetRepository extends EntityRepository implements WorksheetRepository
{
    public function aWorksheetBelongsToClient(string $clientId, string $worksheetId): Worksheet
    {
        $params = [
            "clientId" => $clientId,
            "worksheetId" => $worksheetId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(ClientParticipant::class, "clientProgramParticipation")
                ->leftJoin("clientProgramParticipation.participant", "programParticipation")
                ->leftJoin("clientProgramParticipation.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"));
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->andWhere($qb->expr()->eq("worksheet.id", ":worksheetId"))
                ->leftJoin("worksheet.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aWorksheetBelongsToTeam(string $teamId, string $worksheetId): Worksheet
    {
        $params = [
            "teamId" => $teamId,
            "worksheetId" => $worksheetId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"));
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->andWhere($qb->expr()->eq("worksheet.id", ":worksheetId"))
                ->leftJoin("worksheet.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aWorksheetBelongsToUser(string $userId, string $worksheetId): Worksheet
    {
        $params = [
            "userId" => $userId,
            "worksheetId" => $worksheetId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(UserParticipant::class, "userProgramParticipation")
                ->leftJoin("userProgramParticipation.participant", "programParticipation")
                ->leftJoin("userProgramParticipation.user", "user")
                ->andWhere($participantQb->expr()->eq("user.id", ":userId"));
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->andWhere($qb->expr()->eq("worksheet.id", ":worksheetId"))
                ->leftJoin("worksheet.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aWorksheetInProgram(string $programId, string $worksheetId): Worksheet
    {
        $params = [
            "programId" => $programId,
            "worksheetId" => $worksheetId,
        ];
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->andWhere($qb->expr()->eq("worksheet.id", ":worksheetId"))
                ->leftJoin("worksheet.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allWorksheetBelongsToParticipantInProgram(string $programId, string $participantId, int $page,
            int $pageSize, ?WorksheetFilter $worksheetFilter)
    {
        $params = [
            "programId" => $programId,
            "participantId" => $participantId,
        ];
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->leftJoin("worksheet.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);
        
        $this->applyFilter($qb, $worksheetFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allWorksheetsInProgramParticipationBelongsToClient(string $clientId,
            string $clientProgramParticipationId, int $page, int $pageSize,
            ?WorksheetFilter $worksheetFilter)
    {
        $params = [
            "clientId" => $clientId,
            "clientProgramParticipationId" => $clientProgramParticipationId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(ClientParticipant::class, "clientProgramParticipation")
                ->andWhere($participantQb->expr()->eq("clientProgramParticipation.id", ":clientProgramParticipationId"))
                ->leftJoin("clientProgramParticipation.participant", "programParticipation")
                ->leftJoin("clientProgramParticipation.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->leftJoin("worksheet.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);
        
        $this->applyFilter($qb, $worksheetFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allWorksheetsInProgramParticipationBelongsToTeam(string $teamId, string $teamProgramParticipationId,
            int $page, int $pageSize, ?WorksheetFilter $worksheetFilter)
    {
        $params = [
            "teamId" => $teamId,
            "teamProgramParticipationId" => $teamProgramParticipationId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->andWhere($participantQb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->leftJoin("worksheet.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);
        
        $this->applyFilter($qb, $worksheetFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allWorksheetsInProgramParticipationBelongsToUser(string $userId, string $userProgramParticipationId,
            int $page, int $pageSize, ?WorksheetFilter $worksheetFilter): Worksheet
    {
        $params = [
            "userId" => $userId,
            "userProgramParticipationId" => $userProgramParticipationId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(UserParticipant::class, "userProgramParticipation")
                ->andWhere($participantQb->expr()->eq("userProgramParticipation.id", ":userProgramParticipationId"))
                ->leftJoin("userProgramParticipation.participant", "programParticipation")
                ->leftJoin("userProgramParticipation.user", "user")
                ->andWhere($participantQb->expr()->eq("user.id", ":userId"))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->leftJoin("worksheet.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);
        
        $this->applyFilter($qb, $worksheetFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }
    
    protected function applyFilter(QueryBuilder $qb, ?WorksheetFilter $worksheetFilter): void
    {
        if (!isset($worksheetFilter)) {
            return;
        }
        
        if (!is_null($missionId = $worksheetFilter->getMissionId())) {
            $qb->leftJoin("worksheet.mission", "mission")
                    ->andWhere($qb->expr()->eq("mission.id", ":missionId"))
                    ->setParameter("missionId", $missionId);
        }
        if (!is_null($parentId = $worksheetFilter->getParentId())) {
            $qb->leftJoin("worksheet.parent", "parent")
                    ->andWhere($qb->expr()->eq("parent.id", ":parentId"))
                    ->setParameter("parentId", $parentId);
        }
        if (!is_null($hasParent = $worksheetFilter->isHasParent())) {
            $qb->leftJoin("worksheet.parent", "parent");
            if ($hasParent) {
                $qb->andWhere($qb->expr()->isNotNull("parent.id"));
            } else {
                $qb->andWhere($qb->expr()->isNull("parent.id"));
            }
        }
    }

}
