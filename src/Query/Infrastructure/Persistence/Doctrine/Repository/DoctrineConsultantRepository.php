<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use PDO;
use Query\Application\Auth\Firm\Program\ConsultantRepository as InterfaceForAuthorization;
use Query\Application\Service\Consultant\ConsultantRepository as ConsultantRepository2;
use Query\Application\Service\ConsultantRepository as InterfaceForGuest;
use Query\Application\Service\Firm\Program\ConsultantRepository;
use Query\Application\Service\Personnel\AsProgramConsultant\ConsultantRepository as ConsultantRepository3;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Service\Firm\Program\MentorRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineConsultantRepository extends EntityRepository implements ConsultantRepository, InterfaceForAuthorization, InterfaceForGuest, ConsultantRepository2, MentorRepository, ConsultantRepository3
{

    public function aProgramConsultationOfPersonnel(string $firmId, string $personnelId, string $programConsultationId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programConsultationId' => $programConsultationId,
        ];

        $qb = $this->createQueryBuilder('programConsultation');
        $qb->select('programConsultation')
                ->andWhere($qb->expr()->eq('programConsultation.id', ':programConsultationId'))
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program consultation not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, string $programId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.active', 'true'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramConsultationOfPersonnel(string $firmId, string $personnelId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('programConsultation');
        $qb->select('programConsultation')
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfUnremovedConsultantCorrespondWithPersonnel(
            string $firmId, string $personnelId, string $programId): bool
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('consultant.active', 'true'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'program_firm')
                ->andWhere($qb->expr()->eq('program_firm.id', ':firmId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'personel_firm')
                ->andWhere($qb->expr()->eq('personel_firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty(count($qb->getQuery()->getResult()));
    }

    public function ofId(string $firmId, string $programId, string $consultantId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'consultantId' => $consultantId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allActiveConsultantInProgram(string $programId, int $page, int $pageSize)
    {
        $params = [
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.active', 'true'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActiveConsultant(string $id): Consultant
    {
        $consultant = $this->findOneBy([
            'id' => $id,
            'active' => true,
            
        ]);
        if (empty($consultant)) {
            throw RegularException::notFound('not found: consultant not found');
        }
        return $consultant;
    }

    public function aConsultantBelongsToPersonnel(string $firmId, string $personnelId, string $consultantId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'consultantId' => $consultantId,
        ];
        
        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: consultant not found');
        }
    }

    public function allMentorsAccessibleToParticipant(string $participantId, int $page, int $pageSize)
    {
        $em = $this->getEntityManager();
        $params = [
            "participantId" => $participantId,
        ];
        
        $totalStatement = <<<_TOTAL
SELECT COUNT(Consultant.id) total
FROM Consultant
LEFT JOIN (
    SELECT Participant.Program_id programId
    FROM Participant
    WHERE Participant.id = :participantId
) _a ON _a.programId = Consultant.Program_id
WHERE Consultant.active = true AND _a.programId IS NOT NULL
_TOTAL;
        $totalQuery = $em->getConnection()->prepare($totalStatement);
        $totalQuery->execute($params);
        $total = $totalQuery->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
        
        
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT
    Consultant.id,
    Personnel.id personnelId,
    Personnel.firstName,
    Personnel.lastName,
    IF (_a.id IS NULL, false, true) isDedicatedMentor
FROM Consultant
LEFT JOIN (
    SELECT id, Consultant_id
    FROM DedicatedMentor
    WHERE cancelled = false
)_a ON Consultant_id = Consultant.id
LEFT JOIN Participant ON Participant.Program_id = Consultant.Program_id
LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
WHERE Participant.id = :participantId 
    AND Participant.Program_id IS NOT NULL
    AND  Consultant.active = true
ORDER BY isDedicatedMentor DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        
        $offset = $pageSize * ($page - 1);
        $query = $em->getConnection()->prepare($statement);
        $query->execute($params);
        return [
            'total' => $total,
            'list' => $query->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    public function aMentorInProgram(string $programId, string $mentorId): Consultant
    {
        $params = [
            'programId' => $programId,
            'mentorId' => $mentorId,
        ];
        
        $qb = $this->createQueryBuilder('mentor');
        $qb->select('mentor')
                ->andWhere($qb->expr()->eq('mentor.id', ':mentorId'))
                ->leftJoin('mentor.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: consultant not found');
        }
    }

    public function aConsultantCorrepondWithProgram(string $firmId, string $personnelId, string $programId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programId' => $programId,
        ];
        
        $qb = $this->createQueryBuilder('mentor');
        $qb->select('mentor')
                ->leftJoin('mentor.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: consultant not found');
        }
    }

}
