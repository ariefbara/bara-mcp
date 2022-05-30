<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\ProgramRepository;
use Query\Application\Service\Manager\ProgramRepository as ProgramRepository2;
use Query\Application\Service\ProgramRepository as InterfaceForPublic;
use Query\Domain\Model\Firm\ParticipantTypes;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Task\Dependency\Firm\ProgramRepository as ProgramRepository3;
use Query\Domain\Task\PaginationPayload;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineProgramRepository extends DoctrineEntityRepository implements ProgramRepository, InterfaceForPublic, ProgramRepository2, ProgramRepository3
{

    public function ofId(string $firmId, string $programId): Program
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programId)
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, int $page, int $pageSize, ?string $participantType, ?bool $publishOnly = true)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId);
        
        if (isset($participantType)) {
            $qb->andWhere($qb->expr()->like("program.participantTypes.values", ":participantType"))
                    ->setParameter("participantType", "%$participantType%");
        }
        if ($publishOnly) {
            $qb->andWhere($qb->expr()->eq("program.published", "true"));
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramForUser(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq("program.published", "true"))
                ->andWhere($qb->expr()->like('program.participantTypes.values', ":participantType"))
                ->setParameter("participantType", "%".ParticipantTypes::USER_TYPE."%")
                ->andWhere($qb->expr()->eq('program.removed', 'false'));
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aPublishedProgram(string $id): Program
    {
        $program = $this->findOneBy([
            'id' => $id,
            'published' => true,
            'removed' => false,
        ]);
        
        if (empty($program)) {
            throw RegularException::notFound('not found: program not found');
        }
        return $program;
    }

    public function allPublishedProgram(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq("program.published", "true"))
                ->andWhere($qb->expr()->eq('program.removed', 'false'));
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aProgramOfId(string $id): Program
    {
        return $this->findOneByIdOrDie($id, 'program');
    }

    public function allAvailableProgramsForClient(string $clientId, PaginationPayload $paginationPayload): array
    {
        $parameters = ["clientId" => $clientId];
        $statement = <<<_STATEMENT
SELECT
    Program.id, 
    Program.name, 
    Program.programType, 
    Program.description, 
    Program.participantTypes, 
    FileInfo.folders illustrationPaths,
    FileInfo.name illustrationName
FROM RegistrationPhase
    LEFT JOIN Program ON Program.id = RegistrationPhase.Program_id
    LEFT JOIN Client ON Client.Firm_id = Program.Firm_id
    LEFT JOIN FirmFileInfo ON FirmFileInfo.id = Program.FirmFileInfo_idOfIllustration
    LEFT JOIN FileInfo ON FileInfo.id = FirmFileInfo.FileInfo_id
WHERE Client.id = :clientId
    AND Program.published = true
    AND Program.removed = false
    AND (Program.ParticipantTypes LIKE '%client%' OR Program.ParticipantTypes LIKE '%team%')
    AND ( RegistrationPhase.removed = false
        AND (
            (RegistrationPhase.startDate IS NULL AND RegistrationPhase.endDate IS NULL)
            OR (RegistrationPhase.startDate IS NULL AND RegistrationPhase.endDate >= CURDATE())
            OR (RegistrationPhase.startDate <= CURDATE() AND RegistrationPhase.endDate IS NULL)
            OR (RegistrationPhase.startDate <= CURDATE() AND RegistrationPhase.endDate >= CURDATE())
        )
    )
    AND Program.id NOT IN (
        SELECT Registrant.Program_id
        FROM ClientRegistrant
            LEFT JOIN Registrant ON Registrant.id = ClientRegistrant.Registrant_id
        WHERE ClientRegistrant.Client_id = :clientId
            AND Registrant.concluded = false
    )
    AND Program.id NOT IN (
        SELECT Participant.Program_id
        FROM ClientParticipant
            LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
        WHERE ClientParticipant.Client_id = :clientId
            AND Participant.active = true
    )
GROUP BY Program.id
ORDER BY Program.id ASC
LIMIT {$paginationPayload->getOffset()}, {$paginationPayload->getPageSize()}
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAvailableProgramsForClient($clientId),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    protected function totalCountOfAvailableProgramsForClient(string $clientId): ?int
    {
        $parameters = ["clientId" => $clientId];
        $statement = <<<_STATEMENT
SELECT COUNT(*) Total 
FROM (
    SELECT COUNT(Program.id) total
    FROM RegistrationPhase
        LEFT JOIN Program ON Program.id = RegistrationPhase.Program_id
        LEFT JOIN Client ON Client.Firm_id = Program.Firm_id
    WHERE Client.id = :clientId
        AND Program.published = true
        AND Program.removed = false
        AND (Program.ParticipantTypes LIKE '%client%' OR Program.ParticipantTypes LIKE '%team%')
        AND ( RegistrationPhase.removed = false
            AND (
                (RegistrationPhase.startDate IS NULL AND RegistrationPhase.endDate IS NULL)
                OR (RegistrationPhase.startDate IS NULL AND RegistrationPhase.endDate >= CURDATE())
                OR (RegistrationPhase.startDate <= CURDATE() AND RegistrationPhase.endDate IS NULL)
                OR (RegistrationPhase.startDate <= CURDATE() AND RegistrationPhase.endDate >= CURDATE())
            )
        )
        AND Program.id NOT IN (
            SELECT Registrant.Program_id
            FROM ClientRegistrant
                LEFT JOIN Registrant ON Registrant.id = ClientRegistrant.Registrant_id
            WHERE ClientRegistrant.Client_id = :clientId
                AND Registrant.concluded = false
        )
        AND Program.id NOT IN (
            SELECT Participant.Program_id
            FROM ClientParticipant
                LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
            WHERE ClientParticipant.Client_id = :clientId
                AND Participant.active = true
        )
    GROUP BY Program.id
)_a
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
