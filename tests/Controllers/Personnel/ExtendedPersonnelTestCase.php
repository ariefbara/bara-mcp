<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class ExtendedPersonnelTestCase extends ControllerTestCase
{

    /**
     * 
     * @var RecordOfPersonnel
     */
    protected $personnel;
    /**
     * 
     * @var RecordOfPersonnel
     */
    protected $otherPersonnel;
    protected $personnelUri;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Personnel')->truncate();
        
        $firm = new RecordOfFirm('main');
        $this->personnel = new RecordOfPersonnel($firm, 'main');
        $this->otherPersonnel = new RecordOfPersonnel($firm, 'other');
        
        $this->personnelUri = "/api/personnel";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Personnel')->truncate();
    }
    
    protected function persistPersonnelDependency()
    {
        $this->personnel->firm->insert($this->connection);
        $this->personnel->insert($this->connection);
    }
}
