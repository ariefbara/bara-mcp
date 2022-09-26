<?php

namespace App\Http\Controllers\Personnel\DedicatedMentor;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Task\Personnel\OnDedicatedMentee\ViewAllWorksheetsBelongsToMentee;
use Query\Domain\Task\Personnel\OnDedicatedMentee\ViewAllWorksheetsBelongsToMenteePayload;

class WorksheetOfMenteeController extends DedicatedMentorBaseController
{

    public function showAll(string $dedicatedMentor)
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $task = new ViewAllWorksheetsBelongsToMentee($worksheetRepository);
        $payload = new ViewAllWorksheetsBelongsToMenteePayload($this->getPage(), $this->getPageSize());
        $this->executeQueryTaskOnDedicatedMentee($dedicatedMentor, $task, $payload);

        $result = [];
        $result['total'] = count($payload->result);
        foreach ($payload->result as $worksheet) {
            $parent = empty($worksheet->getParent()) ? null : [
                "id" => $worksheet->getParent()->getId(),
                "name" => $worksheet->getParent()->getName(),
            ];
            $result['list'][] = [
                "id" => $worksheet->getId(),
                "name" => $worksheet->getName(),
                "mission" => [
                    "id" => $worksheet->getMission()->getId(),
                    "name" => $worksheet->getMission()->getName(),
                    "description" => $worksheet->getMission()->getDescription(),
                ],
                "parent" => $parent,
            ];
        }
        return $this->listQueryResponse($result);
    }

}
