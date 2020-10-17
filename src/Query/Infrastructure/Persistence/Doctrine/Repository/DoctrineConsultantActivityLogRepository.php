<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Application\Service\Firm\Personnel\ProgramConsultation\ConsultantActivityLogRepository;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineConsultantActivityLogRepository extends EntityRepository implements ConsultantActivityLogRepository
{

    public function allActivityLogsBelongsToConsultant(
            string $personnelId, string $programConsultationId, int $page, int $pageSize)
    {
        $params = [
            "personnelId" => $personnelId,
            "consultantId" => $programConsultationId,
        ];
        
        $qb = $this->createQueryBuilder("consultantActivityLog");
        $qb->select("consultantActivityLog")
                ->leftJoin("consultantActivityLog.consultant", "consultant")
                ->andWhere($qb->expr()->eq("consultant.id", ":consultantId"))
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
