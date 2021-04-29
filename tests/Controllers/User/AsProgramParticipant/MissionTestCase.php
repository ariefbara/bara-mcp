<?php

namespace Tests\Controllers\User\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfMission,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm
};

class MissionTestCase extends AsProgramParticipantTestCase
{
    protected $missionUri;
    /**
     *
     * @var RecordOfMission
     */
    protected $mission;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->missionUri = $this->asProgramParticipantUri . "/missions";
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        
        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $this->mission = new RecordOfMission($program, $worksheetForm, 999, null);
        $this->mission->published = true;
        $this->connection->table('Mission')->insert($this->mission->toArrayForDbEntry());
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
    }
}
