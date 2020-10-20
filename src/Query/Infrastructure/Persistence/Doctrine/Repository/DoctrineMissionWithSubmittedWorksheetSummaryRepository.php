<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use PDO;
use Query\Application\Service\Firm\{
    Client\AsProgramParticipant\MissionWithSubmittedWorksheetSummaryRepository as InterfaceForClient,
    Team\AsProgramParticipant\MissionWithSubmittedWorksheetSummaryRepository as InterfaceForTeam
};

class DoctrineMissionWithSubmittedWorksheetSummaryRepository implements InterfaceForTeam, InterfaceForClient
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

    public function allMissionInProgramIncludeSubmittedWorksheetFromClient(
            string $programId, string $clientId, int $page, int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT M.id, M.name, M.description, M.position, _b.submittedWorksheet
FROM Mission M
LEFT JOIN (
    SELECT Participant.id participantId, Participant.Program_id
    FROM ClientParticipant
        LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
    WHERE Participant.active = true
        AND ClientParticipant.Client_id = :clientId
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
            "clientId" => $clientId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
