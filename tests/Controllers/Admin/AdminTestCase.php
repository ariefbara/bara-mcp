<?php

namespace Tests\Controllers\Admin;

use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\RecordOfAdmin
};

class AdminTestCase extends ControllerTestCase
{
    /**
     *
     * @var RecordOfAdmin
     */
    protected $admin;
    /**
     *
     * @var RecordOfAdmin
     */
    protected $removedAdmin;
    protected $adminUri = '/api/admin';


    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Admin')->truncate();
        
        $this->admin = new RecordOfAdmin('admin', 'admin@email.org', 'password123');
        $this->removedAdmin = new RecordOfAdmin('removed admin', 'removed_admin@email.org', 'password123');
        $this->removedAdmin->removed = true;
        $this->connection->table('Admin')->insert($this->admin->toArrayForDbEntry());
        $this->connection->table('Admin')->insert($this->removedAdmin->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Admin')->truncate();
    }
}
