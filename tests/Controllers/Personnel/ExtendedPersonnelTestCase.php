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
    protected $personnelUri;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Personnel')->truncate();
        
        $firm = new RecordOfFirm(99);
        $this->personnel = new RecordOfPersonnel($firm, 99);
        
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
