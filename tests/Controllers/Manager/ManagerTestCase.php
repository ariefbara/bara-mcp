<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\Firm\RecordOfManager,
    RecordPreparation\RecordOfFirm
};

class ManagerTestCase extends ControllerTestCase
{
    /**
     *
     * @var RecordOfFirm
     */
    protected $firm;
    /**
     *
     * @var RecordOfManager
     */
    protected $manager;
    /**
     *
     * @var RecordOfManager
     */
    protected $removedManager;
    protected $managerUri = "/api/manager";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
        
        $this->firm = new RecordOfFirm(0, 'firm_0');
        $this->connection->table('Firm')->insert($this->firm->toArrayForDbEntry());
        
        $this->manager = new RecordOfManager($this->firm, 0, 'manager@email.org', 'password123');
        $this->removedManager = new RecordOfManager($this->firm, 1, 'removed_manager@email.org', 'password123');
        $this->removedManager->removed = true;
        $this->connection->table('Manager')->insert($this->manager->toArrayForDbEntry());
        $this->connection->table('Manager')->insert($this->removedManager->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
    }
}
