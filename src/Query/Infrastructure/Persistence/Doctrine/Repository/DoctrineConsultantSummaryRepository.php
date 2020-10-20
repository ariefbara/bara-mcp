<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use PDO;
use Query\Application\Service\Firm\Program\ConsultantSummaryRepository;

class DoctrineConsultantSummaryRepository implements ConsultantSummaryRepository
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function allConsultantSummaryInProgram(string $programId, int $page, int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT 
    C.id,
    CONCAT(Personnel.firstName, ' ', Personnel.lastName) 'name',
    _f.consultationRequestCount,
    _g.consultationSessionCount,
    _b.unconcludedConsultationRequestCount,
    _c.rejectedConsultationRequestCount,
    _d.cancelledConsultationRequestCount,
    _e.commentCount,
    _a.commentCountInLastSevenDays,
    _e.lastSubmitTime
FROM Consultant C
LEFT JOIN Personnel ON Personnel.id = C.Personnel_id
LEFT OUTER JOIN (
    SELECT CC.Consultant_id, COUNT(CC.id) commentCountInLastSevenDays
    FROM ConsultantComment CC
    LEFT JOIN Comment C ON C.id = CC.Comment_id
    WHERE C.submitTime >= DATE(NOW()) - INTERVAL 7 DAY
    GROUP BY Consultant_id
)_a ON _a.Consultant_id = C.id
LEFT OUTER JOIN (
    SELECT Consultant_id, COUNT(id) unconcludedConsultationRequestCount
    FROM ConsultationRequest
    WHERE concluded = false
    GROUP BY Consultant_id
)_b ON _b.Consultant_id = C.id
LEFT OUTER JOIN (
    SELECT Consultant_id, COUNT(id) rejectedConsultationRequestCount
    FROM ConsultationRequest
    WHERE status = 'rejected'
    GROUP BY Consultant_id
)_c ON _c.Consultant_id = C.id
LEFT OUTER JOIN (
    SELECT Consultant_id, COUNT(id) cancelledConsultationRequestCount
    FROM ConsultationRequest
    WHERE status = 'cancelled'
    GROUP BY Consultant_id
)_d ON _d.Consultant_id = C.id
LEFT OUTER JOIN (
    SELECT CC.Consultant_id, COUNT(CC.id) commentCount, MAX(C.submitTime) lastSubmitTime
    FROM ConsultantComment CC
    LEFT JOIN Comment C ON C.id = CC.Comment_id
    GROUP BY CC.Consultant_id
)_e ON _e.Consultant_id = C.id
LEFT OUTER JOIN (
    SELECT Consultant_id, COUNT(id) consultationRequestCount
    FROM ConsultationRequest
    GROUP BY Consultant_id
)_f ON _f.Consultant_id = C.id
LEFT OUTER JOIN (
    SELECT Consultant_id, COUNT(id) consultationSessionCount
    FROM ConsultationSession
    GROUP BY Consultant_id
)_g ON _g.Consultant_id = C.id
WHERE C.Program_Id = :programId
    AND C.removed = false
ORDER BY consultationSessionCount DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "programId" => $programId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalActiveConsultantInProgram(string $programId): int
    {
        $statement = <<<_STATEMENT
SELECT COUNT(Consultant.id) total
FROM Consultant
WHERE Consultant.Program_id = :programId
    AND Consultant.removed = false
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "programId" => $programId,
        ];
        $query->execute($params);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["total"];
    }

}
