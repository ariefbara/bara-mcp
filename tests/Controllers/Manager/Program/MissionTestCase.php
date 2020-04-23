<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\RecordOfMission,
    RecordPreparation\Firm\RecordOfWorksheetForm,
    RecordPreparation\Shared\RecordOfForm
};

class MissionTestCase extends ProgramTestCase
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
        $this->missionUri = $this->programUri . "/{$this->program->id}/missions";
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        
        $form = new RecordOfForm('main-worksheet-form');
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($this->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $this->mission = new RecordOfMission($this->program, $worksheetForm, 'root', null);
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
