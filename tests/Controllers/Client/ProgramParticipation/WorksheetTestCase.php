<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\ {
    Client\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Participant\RecordOfWorksheet,
    RecordPreparation\Firm\Program\RecordOfMission,
    RecordPreparation\Firm\RecordOfWorksheetForm,
    RecordPreparation\Shared\RecordOfForm,
    RecordPreparation\Shared\RecordOfFormRecord
};

class WorksheetTestCase extends ProgramParticipationTestCase
{

    protected $worksheetUri;

    /**
     *
     * @var RecordOfForm
     */
    protected $form;
    /**
     *
     * @var RecordOfWorksheetForm
     */
    protected $worksheetForm;
    /**
     *
     * @var RecordOfMission
     */
    protected $mission;

    /**
     *
     * @var RecordOfWorksheet
     */
    protected $worksheet;
    protected $worksheetInput;
    protected $worksheetResponse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetUri = $this->programParticipationUri . "/{$this->programParticipation->program->id}/worksheets";
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();


        $this->form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($this->form->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($this->form, 0);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());

        $this->worksheetForm = new RecordOfWorksheetForm($this->programParticipation->program->firm, $this->form);
        $this->connection->table('WorksheetForm')->insert($this->worksheetForm->toArrayForDbEntry());

        $this->mission = new RecordOfMission($this->programParticipation->program, $this->worksheetForm, 0, null);
        $this->connection->table('Mission')->insert($this->mission->toArrayForDbEntry());

        $this->worksheet = new RecordOfWorksheet($this->programParticipation->participant, $formRecord, $this->mission);
        $this->connection->table('Worksheet')->insert($this->worksheet->toArrayForDbEntry());

        $this->worksheetInput = [
            "name" => "new worksheet name",
            "missionId" => $this->mission->id,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        $this->worksheetResponse = [
            "name" => $this->worksheetInput['name'],
            "parent" => null,
            "mission" => [
                "id" => $this->mission->id,
                "name" => $this->mission->name,
                "worksheetForm" => [
                    "id" => $this->mission->worksheetForm->id,
                    "name" => $this->mission->worksheetForm->form->name,
                    "description" => $this->mission->worksheetForm->form->description,
                ],
            ],
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
    }
    

}
