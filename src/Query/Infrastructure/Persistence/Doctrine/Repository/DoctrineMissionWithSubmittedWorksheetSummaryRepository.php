<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use PDO;
use Query\Application\Service\Firm\Team\AsProgramParticipant\MissionWithSubmittedWorksheetSummaryRepository;

class DoctrineMissionWithSubmittedWorksheetSummaryRepository implements MissionWithSubmittedWorksheetSummaryRepository
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

    public function allMissionInProgramIncludeSubmittedWorksheetFromTeam(
            string $programId, string $teamId, int $page, int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);
/*
        $statement = <<<_STATEMENT
SELECT _b.id, _b.name, _b.description, _b.position, _c.submittedWorksheet
FROM (
    SELECT Participant.id participantId, Participant.Program_id programId
    FROM TeamParticipant
        LEFT JOIN Participant ON Participant.id = TeamParticipant.Participant_id
    WHERE Participant.active = true
        AND Participant.Program_id = :programId
        AND TeamParticipant.Team_id = :teamId
)_a
LEFT JOIN (
    SELECT Mission.id, Mission.name, Mission.description, Mission.position, Mission.Program_id programId
    FROM Mission
    WHERE Mission.published = true
    GROUP BY id
)_b ON _b.programId = _a.programId
LEFT OUTER JOIN (
    SELECT 
        Worksheet.Participant_id participantId,
        Worksheet.Mission_id missionId,
        COUNT(Worksheet.id) AS submittedWorksheet
    FROM Worksheet
    WHERE Worksheet.removed = false
    GROUP BY missionId, participantId
)_c ON _c.missionId = _b.id AND _c.participantId = _a.participantId                
ORDER BY _b.position ASC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
 * 
 */
        
        $statement = <<<_STATEMENT
SELECT M.id, M.name, M.description, M.position, _b.submittedWorksheet
FROM Mission M
LEFT JOIN (
    SELECT Participant.id participantId, Participant.Program_id
    FROM TeamParticipant
        LEFT JOIN Participant ON Participant.id = TeamParticipant.Participant_id
    WHERE Participant.active = true
        AND TeamParticipant.Team_id = :teamId
)_a ON _a.Program_id = M.Program_id
LEFT OUTER JOIN (
    SELECT W.Participant_id, W.Mission_id, COUNT(W.id) AS submittedWorksheet
    FROM Worksheet W
    WHERE W.removed = false
    GROUP BY Mission_id, Participant_id
)_b ON _b.Mission_id = M.id AND _b.Participant_id = _a.participantId
WHERE M.Program_id = :programId
ORDER BY M.position ASC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "programId" => $programId,
            "teamId" => $teamId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalMissionInProgram(string $programId): int
    {
        $statement = <<<_STATEMENT
SELECT COUNT(Mission.id) total
FROM Mission
WHERE Mission.Program_id = :programId
    AND Mission.published = true
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
