<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant\Participant;

use Tests\Controllers\{
    Personnel\AsProgramConsultant\ParticipantTestCase,
    RecordPreparation\Firm\Program\Participant\RecordOfWorksheet,
    RecordPreparation\Firm\Program\RecordOfMission,
    RecordPreparation\Firm\RecordOfWorksheetForm,
    RecordPreparation\Shared\RecordOfForm,
    RecordPreparation\Shared\RecordOfFormRecord
};

class WorksheetTestCase extends ParticipantTestCase
{

    protected $worksheetUri;

    /**
     *
     * @var RecordOfWorksheet
     */
    protected $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetUri = $this->participantUri . "/{$this->participant->id}/worksheets";
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();

        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, 0);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($this->personnel->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $mission = new RecordOfMission($this->consultant->program, $worksheetForm, 0, null);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());

        $this->worksheet = new RecordOfWorksheet($this->participant, $formRecord, $mission, 0);
        $this->connection->table('Worksheet')->insert($this->worksheet->toArrayForDbEntry());
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
