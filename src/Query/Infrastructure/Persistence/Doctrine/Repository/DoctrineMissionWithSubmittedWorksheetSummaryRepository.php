<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use PDO;
use Query\Application\Service\ {
    Firm\Client\AsProgramParticipant\MissionWithSubmittedWorksheetSummaryRepository as InterfaceForClient,
    Firm\Team\AsProgramParticipant\MissionWithSubmittedWorksheetSummaryRepository as InterfaceForTeam,
    User\AsProgramParticipant\MissionWithSubmittedWorksheetSummaryRepository as InterfaceForUser
};

class DoctrineMissionWithSubmittedWorksheetSummaryRepository implements InterfaceForTeam, InterfaceForClient, InterfaceForUser
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
SELECT
    Mission.id,
    Mission.name,
    Mission.description,
    Mission.position,
    Mission.WorksheetForm_id worksheetFormId,
    Form.name worksheetFormName,
    _a.submittedWorksheet
FROM Mission
    LEFT JOIN WorksheetForm ON WorksheetForm.id = Mission.WorksheetForm_id
    LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
    LEFT JOIN (
        SELECT COUNT(Worksheet.id) submittedWorksheet, Worksheet.Mission_id
        FROM Worksheet
            INNER JOIN Participant ON Worksheet.Participant_id = Participant.id AND Participant.active = true
            INNER JOIN TeamParticipant ON Participant.id = TeamParticipant.id AND TeamParticipant.Team_id = :teamId
        WHERE Worksheet.removed = false
        GROUP BY Mission_id
    )_a ON _a.Mission_id = Mission.id
WHERE Mission.Program_id = :programId
    AND Mission.Published = true
ORDER BY CAST(position as UNSIGNED INTEGER) ASC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
//        $statement = <<<_STATEMENT
//SELECT _a.id, _a.name, _a.description, _a.position, SUM(_a.submittedWorksheet) submittedWorksheet, _a.WorksheetForm_id worksheetFormId, Form.name worksheetFormName 
//FROM (
//	SELECT M.id, M.name, M.description, M.position, _a1.submittedWorksheet, M.WorksheetForm_id
//	FROM (
//	    SELECT Participant.id participantId, Participant.Program_id, _a1a.Mission_id, _a1a.submittedWorksheet
//	    FROM TeamParticipant
//		LEFT JOIN Participant ON Participant.id = TeamParticipant.Participant_id
//		LEFT JOIN (
//		    SELECT Worksheet.Participant_id, Worksheet.Mission_id, COUNT(Worksheet.id) AS submittedWorksheet
//		    FROM Worksheet
//		    WHERE Worksheet.removed = false
//		    GROUP BY Worksheet.Mission_id, Worksheet.Participant_id
//		)_a1a ON _a1a.Participant_id = Participant.id
//	    WHERE Participant.active = true
//		AND TeamParticipant.Team_id = :teamId
//	)_a1
//	LEFT JOIN Mission M ON M.id = _a1.Mission_id
//	WHERE M.Program_id = :programId AND M.published = true
//
//	UNION 
//            
//        SELECT M.id, M.name, M.description, M.position, 0 submittedWorksheet, M.WorksheetForm_id
//	FROM Mission M
//	LEFT JOIN WorksheetForm ON WorksheetForm.id = M.WorksheetForm_id
//	LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
//	WHERE M.Program_id = :programId AND M.published = true
//)_a
//LEFT JOIN WorksheetForm ON WorksheetForm.id = _a.WorksheetForm_id
//LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
//GROUP BY id
//ORDER BY CAST(position as UNSIGNED INTEGER) ASC
//LIMIT {$offset}, {$pageSize}
//_STATEMENT;

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
SELECT
    Mission.id,
    Mission.name,
    Mission.description,
    Mission.position,
    Mission.WorksheetForm_id worksheetFormId,
    Form.name worksheetFormName,
    _a.submittedWorksheet
FROM Mission
    LEFT JOIN WorksheetForm ON WorksheetForm.id = Mission.WorksheetForm_id
    LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
    LEFT JOIN (
        SELECT COUNT(Worksheet.id) submittedWorksheet, Worksheet.Mission_id
        FROM Worksheet
            INNER JOIN Participant ON Worksheet.Participant_id = Participant.id AND Participant.active = true
            INNER JOIN ClientParticipant ON Participant.id = ClientParticipant.id AND ClientParticipant.Client_id = :clientId
        WHERE Worksheet.removed = false
        GROUP BY Mission_id
    )_a ON _a.Mission_id = Mission.id
WHERE Mission.Program_id = :programId
    AND Mission.Published = true
ORDER BY CAST(position as UNSIGNED INTEGER) ASC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
//        $statement = <<<_STATEMENT
//SELECT _a.id, _a.name, _a.description, _a.position, SUM(_a.submittedWorksheet) submittedWorksheet, _a.WorksheetForm_id worksheetFormId, Form.name worksheetFormName 
//FROM (
//	SELECT M.id, M.name, M.description, M.position, _a1.submittedWorksheet, M.WorksheetForm_id
//	FROM (
//	    SELECT Participant.id participantId, Participant.Program_id, _a1a.Mission_id, _a1a.submittedWorksheet
//	    FROM ClientParticipant
//		LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
//		LEFT JOIN (
//		    SELECT Worksheet.Participant_id, Worksheet.Mission_id, COUNT(Worksheet.id) AS submittedWorksheet
//		    FROM Worksheet
//		    WHERE Worksheet.removed = false
//		    GROUP BY Worksheet.Mission_id, Worksheet.Participant_id
//		)_a1a ON _a1a.Participant_id = Participant.id
//	    WHERE Participant.active = true AND ClientParticipant.Client_id = :clientId
//	)_a1
//	LEFT JOIN Mission M ON M.id = _a1.Mission_id
//	WHERE M.Program_id = :programId AND M.published = true
//
//	UNION 
//            
//        SELECT M.id, M.name, M.description, M.position, 0 submittedWorksheet, M.WorksheetForm_id
//	FROM Mission M
//	LEFT JOIN WorksheetForm ON WorksheetForm.id = M.WorksheetForm_id
//	LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
//	WHERE M.Program_id = :programId AND M.published = true
//)_a
//LEFT JOIN WorksheetForm ON WorksheetForm.id = _a.WorksheetForm_id
//LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
//GROUP BY id
//ORDER BY CAST(position as UNSIGNED INTEGER) ASC
//LIMIT {$offset}, {$pageSize}
//_STATEMENT;

        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "programId" => $programId,
            "clientId" => $clientId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allMissionInProgramIncludeSubmittedWorksheetFromUser(string $programId, string $userId, int $page,
            int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT
    Mission.id,
    Mission.name,
    Mission.description,
    Mission.position,
    Mission.WorksheetForm_id worksheetFormId,
    Form.name worksheetFormName,
    _a.submittedWorksheet
FROM Mission
    LEFT JOIN WorksheetForm ON WorksheetForm.id = Mission.WorksheetForm_id
    LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
    LEFT JOIN (
        SELECT COUNT(Worksheet.id) submittedWorksheet, Worksheet.Mission_id
        FROM Worksheet
            INNER JOIN Participant ON Worksheet.Participant_id = Participant.id AND Participant.active = true
            INNER JOIN UserParticipant ON Participant.id = UserParticipant.id AND UserParticipant.User_id = :userId
        WHERE Worksheet.removed = false
        GROUP BY Mission_id
    )_a ON _a.Mission_id = Mission.id
WHERE Mission.Program_id = :programId
    AND Mission.Published = true
ORDER BY CAST(position as UNSIGNED INTEGER) ASC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
//        $statement = <<<_STATEMENT
//SELECT _a.id, _a.name, _a.description, _a.position, SUM(_a.submittedWorksheet) submittedWorksheet, _a.WorksheetForm_id worksheetFormId, Form.name worksheetFormName 
//FROM (
//	SELECT M.id, M.name, M.description, M.position, _a1.submittedWorksheet, M.WorksheetForm_id
//	FROM (
//	    SELECT Participant.id participantId, Participant.Program_id, _a1a.Mission_id, _a1a.submittedWorksheet
//	    FROM UserParticipant
//		LEFT JOIN Participant ON Participant.id = UserParticipant.Participant_id
//		LEFT JOIN (
//		    SELECT Worksheet.Participant_id, Worksheet.Mission_id, COUNT(Worksheet.id) AS submittedWorksheet
//		    FROM Worksheet
//		    WHERE Worksheet.removed = false
//		    GROUP BY Worksheet.Mission_id, Worksheet.Participant_id
//		)_a1a ON _a1a.Participant_id = Participant.id
//	    WHERE Participant.active = true AND UserParticipant.User_id = :userId
//	)_a1
//	LEFT JOIN Mission M ON M.id = _a1.Mission_id
//	WHERE M.Program_id = :programId AND M.published = true
//
//	UNION 
//            
//        SELECT M.id, M.name, M.description, M.position, 0 submittedWorksheet, M.WorksheetForm_id
//	FROM Mission M
//	LEFT JOIN WorksheetForm ON WorksheetForm.id = M.WorksheetForm_id
//	LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
//	WHERE M.Program_id = :programId AND M.published = true
//)_a
//LEFT JOIN WorksheetForm ON WorksheetForm.id = _a.WorksheetForm_id
//LEFT JOIN Form ON Form.id = WorksheetForm.Form_id
//GROUP BY id
//ORDER BY CAST(position as UNSIGNED INTEGER) ASC
//LIMIT {$offset}, {$pageSize}
//_STATEMENT;

        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "programId" => $programId,
            "userId" => $userId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
