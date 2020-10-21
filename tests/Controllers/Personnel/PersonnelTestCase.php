<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\{
    ControllerTestCase,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\RecordOfFirm
};

class PersonnelTestCase extends ControllerTestCase
{

    protected $personnelUri = "/api/personnel";

    /**
     *
     * @var RecordOfPersonnel
     */
    protected $personnel;

    /**
     *
     * @var RecordOfPersonnel
     */
    protected $removedPersonnel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Personnel')->truncate();

        $firm = new RecordOfFirm(999, 'firm_identifier');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());

        $this->personnel = new RecordOfPersonnel($firm, 999, 'adi@barapraja.com', 'password123');
        $this->removedPersonnel = new RecordOfPersonnel($firm, 'removed', 'removed_personnel@email.org', 'password123');
        $this->removedPersonnel->removed = true;
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->removedPersonnel->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Personnel')->truncate();
    }

}
