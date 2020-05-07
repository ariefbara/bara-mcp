<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\RecordOfFirm
};

class FirmTestCase extends ControllerTestCase
{
    protected $firmUri = "/api/guest/firms";
    /**
     *
     * @var RecordOfFirm
     */
    protected $firm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        
        $this->firm = new RecordOfFirm(0, 'firm_identifier');
        $this->connection->table('Firm')->insert($this->firm->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
    }
}
