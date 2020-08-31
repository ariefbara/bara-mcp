<?php

namespace Tests\Controllers\User;

use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\RecordOfUser
};

class UserTestCase extends ControllerTestCase
{
    protected $userUri = '/api/user';
    /**
     *
     * @var RecordOfUser
     */
    protected $user;
    /**
     *
     * @var RecordOfUser
     */
    protected $inactiveUser;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('User')->truncate();
        
        $this->user = new RecordOfUser(0);
        $this->user->email = 'purnama.adi@gmail.com';
        $this->inactiveUser = new RecordOfUser('inactive');
        $this->inactiveUser->activated = false;
        $this->connection->table('User')->insert($this->user->toArrayForDbEntry());
        $this->connection->table('User')->insert($this->inactiveUser->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('User')->truncate();
    }
}
