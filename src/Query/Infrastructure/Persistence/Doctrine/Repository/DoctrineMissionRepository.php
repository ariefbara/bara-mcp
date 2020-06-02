<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use PDO;
use Query\{
    Application\Service\Client\ProgramParticipation\MissionRepository as InterfaceForProgramParticipant,
    Application\Service\Firm\Program\MissionRepository,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\Program\Participant
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineMissionRepository extends EntityRepository implements MissionRepository, InterfaceForProgramParticipant
{

    public function ofId(ProgramCompositionId $programCompositionId, string $missionId): Mission
    {
        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->setParameter('missionId', $missionId)
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $programCompositionId->getFirmId())
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: mission not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize, ?string $position)
    {
        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $programCompositionId->getFirmId());

        if (!empty($position)) {
            $qb->andWhere($qb->expr()->eq('mission.position', ':position'))
                    ->setParameter('position', $position);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allMissionsContainSubmittedWorksheetCount(
            ProgramParticipationCompositionId $programParticipationCompositionId)
    {
        $statement = <<<_STATEMENT
SELECT y.id, y.name, y.description, y.position, z.submittedWorksheet
FROM (
    SELECT Mission.id, Mission.name, Mission.description, Mission.position
    FROM Mission
        LEFT JOIN Program ON Program.id = Mission.Program_id
    WHERE Mission.published = true
        AND Program.id = (
            SELECT Program.id
            FROM Participant
                LEFT JOIN Program ON Program.id = Participant.Program_id
                LEFT JOIN Client ON Client.id = Participant.Client_id
            WHERE Participant.id = :participantId
                AND Participant.active = true
                AND Client.id = :clientId
        )
)y
LEFT JOIN (
    SELECT 
        Worksheet.Mission_id missionId,
        COUNT(Worksheet.id) AS submittedWorksheet
    FROM Worksheet
        LEFT JOIN Mission ON Mission.id = Worksheet.Mission_id
        LEFT JOIN Participant ON Participant.id = Worksheet.Participant_id
        LEFT JOIN Client ON Client.id = Participant.Client_id
    WHERE Worksheet.removed = false
        AND Participant.id = :participantId
        AND Participant.active = true
        AND Client.id = :clientId
    GROUP BY missionId
)z ON z.missionId = y.id
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = [
            'participantId' => $programParticipationCompositionId->getProgramParticipationId(),
            'clientId' => $programParticipationCompositionId->getClientId(),
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aMissionInProgramWhereParticipantParticipate(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $missionId): Mission
    {
        $params = [
            'participantId' => $programParticipationCompositionId->getProgramParticipationId(),
            'clientId' => $programParticipationCompositionId->getClientId(),
            'missionId' => $missionId,
        ];

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('x_program.id')
                ->from(Participant::class, 'x_participant')
                ->andWhere($subQuery->expr()->eq('x_participant.id', ':participantId'))
                ->andWhere($subQuery->expr()->eq('x_participant.active', 'true'))
                ->leftJoin('x_participant.client', 'x_client')
                ->andWhere($subQuery->expr()->eq('x_client.id', ':clientId'))
                ->leftJoin('x_participant.program', 'x_program')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->andWhere($qb->expr()->eq('mission.published', 'true'))
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->in('program.id', $subQuery->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: mission not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aMissionByPositionInProgramWhereParticipantParticipate(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $position): Mission
    {
        $params = [
            'participantId' => $programParticipationCompositionId->getProgramParticipationId(),
            'clientId' => $programParticipationCompositionId->getClientId(),
            'position' => $position,
        ];

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('x_program.id')
                ->from(Participant::class, 'x_participant')
                ->andWhere($subQuery->expr()->eq('x_participant.id', ':participantId'))
                ->andWhere($subQuery->expr()->eq('x_participant.active', 'true'))
                ->leftJoin('x_participant.client', 'x_client')
                ->andWhere($subQuery->expr()->eq('x_client.id', ':clientId'))
                ->leftJoin('x_participant.program', 'x_program')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->andWhere($qb->expr()->eq('mission.position', ':position'))
                ->andWhere($qb->expr()->eq('mission.published', 'true'))
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->in('program.id', $subQuery->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: mission not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
