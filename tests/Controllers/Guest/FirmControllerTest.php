<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\RecordPreparation\RecordOfFirm;

class FirmControllerTest extends FirmTestCase
{
    protected $firmOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmOne = new RecordOfFirm(1, 'firm_one');
        $this->connection->table('Firm')->insert($this->firmOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->firm->id,
            "name" => $this->firm->name,
        ];
        $uri = $this->firmUri . "/{$this->firm->id}";
        $this->get($uri)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->firm->id,
                    "name" => $this->firm->name,
                ],
                [
                    "id" => $this->firmOne->id,
                    "name" => $this->firmOne->name,
                ],
            ],
        ];
        $this->get($this->firmUri)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
