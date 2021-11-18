<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class EnhanceManagerTestCase extends ControllerTestCase
{
    /**
     * 
     * @var RecordOfManager
     */
    protected $managerOne;
    protected $managerUri = '/api/manager';

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
        
        $firmOne = new RecordOfFirm('1');
        $this->managerOne = new RecordOfManager($firmOne, '1');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
    }
    
    protected function insertPreparedManagerRecord(): void
    {
        $this->managerOne->firm->insert($this->connection);
        $this->managerOne->insert($this->connection);
    }
    
}
