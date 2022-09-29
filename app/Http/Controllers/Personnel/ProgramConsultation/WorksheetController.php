<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Domain\Model\Firm\Program\Mission;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetFilter;
use Query\Domain\Task\InProgram\ViewAllActiveWorksheet;
use Query\Domain\Task\InProgram\ViewAllActiveWorksheetPayload;
use Query\Domain\Task\InProgram\ViewWorksheet;

class WorksheetController extends ConsultantBaseController
{

    public function viewAll($consultantId)
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $task = new ViewAllActiveWorksheet($worksheetRepository);

        $worksheetFilter = new WorksheetFilter($this->getPage(), $this->getPageSize());
        $worksheetFilter->setParticipantId($this->stripTagQueryRequest('participantId'));
        $payload = new ViewAllActiveWorksheetPayload($worksheetFilter);

        $this->executeProgramQueryTask($consultantId, $task, $payload);

        $result = [];
        $result['total'] = count($payload->result);
        foreach ($payload->result as $worksheet) {
            $result['list'][] = $this->overviewDataOfWorksheet($worksheet);
        }
        return $this->listQueryResponse($result);
    }

    public function viewDetail($consultantId, $worksheetId)
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $task = new ViewWorksheet($worksheetRepository);
        $payload = new CommonViewDetailPayload($worksheetId);

        $this->executeProgramQueryTask($consultantId, $task, $payload);

        return $this->singleQueryResponse($this->arrayDataOfWorksheet($payload->result));
    }

    protected function overviewDataOfWorksheet(Worksheet $worksheet): array
    {
        return [
            'id' => $worksheet->getId(),
            'name' => $worksheet->getName(),
            'mission' => $this->arrayDataOfMission($worksheet->getMission()),
            'parent' => $this->arrayDataOfParentWorksheet($worksheet->getParent()),
        ];
    }

    protected function arrayDataOfWorksheet(Worksheet $worksheet): array
    {
        $data = (new FormRecordToArrayDataConverter())->convert($worksheet);
        $data['id'] = $worksheet->getId();
        $data['name'] = $worksheet->getName();
        $data['parent'] = $this->arrayDataOfParentWorksheet($worksheet->getParent());
        $data['mission'] = $this->arrayDataOfMission($worksheet->getMission());
        return $data;
    }

    protected function arrayDataOfParentWorksheet(?Worksheet $parentWorksheet): ?array
    {
        return empty($parentWorksheet) ? null : [
            "id" => $parentWorksheet->getId(),
            "name" => $parentWorksheet->getName(),
            "parent" => $this->arrayDataOfParentWorksheet($parentWorksheet->getParent()),
        ];
    }

    protected function arrayDataOfMission(Mission $mission): array
    {
        return [
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
            "worksheetForm" => [
                "id" => $mission->getWorksheetForm()->getId(),
                "name" => $mission->getWorksheetForm()->getName(),
                "description" => $mission->getWorksheetForm()->getDescription(),
            ],
        ];
    }

}
