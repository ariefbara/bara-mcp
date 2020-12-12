<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\ProgramsProfileFormRepository;
use Query\Domain\Model\Firm\Program\ProgramsProfileForm;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineProgramsProfileFormRepository extends EntityRepository implements ProgramsProfileFormRepository
{

    public function aProgramsProfileFormInProgram(
            string $firmId, string $programId, string $programsProfileFormId): ProgramsProfileForm
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "programsProfileFormId" => $programsProfileFormId,
        ];

        $qb = $this->createQueryBuilder("programsProfileForm");
        $qb->select("programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ":programsProfileFormId"))
                ->leftJoin("programsProfileForm.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: programs profile form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allProgramsProfileFormsInProgram(string $firmId, string $programId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("programsProfileForm");
        $qb->select("programsProfileForm")
                ->leftJoin("programsProfileForm.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
