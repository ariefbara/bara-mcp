<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Coordinator\ConsultantRepository as InterfaceFormCoordinator;
use Firm\Application\Service\Firm\Program\ConsultantRepository;
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Firm\Application\Service\Manager\ConsultantRepository as InterfaceForManager;
use Firm\Domain\Model\Firm\Program\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Task\MeetingInitiator\UserRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineConsultantRepository extends DoctrineEntityRepository implements ConsultantRepository, InterfaceForManager, InterfaceFormCoordinator,
        UserRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $consultantId): Consultant
    {
        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->setParameter('consultantId', $consultantId)
                ->leftJoin('consultant.program', 'program')
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
            $errorDetail = "not found: consultant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aConsultantOfId(string $consultantId): Consultant
    {
        $consultant = $this->findOneBy(["id" => $consultantId]);
        if (empty($consultant)) {
            $errorDetail = "not found: consultant not found";
            throw RegularException::notFound($errorDetail);
        }
        return $consultant;
    }

    public function aConsultantCorrespondWithProgram(string $firmId, string $personnelId, string $programId): Consultant
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("consultant");
        $qb->select("consultant")
                ->leftJoin("consultant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aUserOfId(string $id): CanAttendMeeting
    {
        return $this->findOneByIdOrDie($id, 'consultant');
    }

}
