<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class ExtendedManagerTestCase extends ControllerTestCase
{

    /**
     * 
     * @var RecordOfManager
     */
    protected $manager;
    protected $managerUri = 'api/manager';

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();

        $firm = new RecordOfFirm('main');
        $this->manager = new RecordOfManager($firm, 'main');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
    }

    protected function persistManagerDependency(): void
    {
        $this->manager->firm->insert($this->connection);
        $this->manager->insert($this->connection);
    }

}
