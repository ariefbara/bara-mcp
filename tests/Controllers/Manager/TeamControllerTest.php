<?php

namespace Tests\Controllers\Manager;

class TeamControllerTest extends EnhanceManagerTestCase
{
    protected $teamOne;
    protected $teamTwo;
    
    protected $clientOne;
    protected $clientTwo;

    protected $member_11_t1;
    protected $member_12_t1;
    
    protected $addRequest;

    protected $showAllUri;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        
        $this->showAllUri = $this->managerUri . "/teams";
        
        $firm = $this->managerOne->firm;
        
        $this->clientOne = new \Tests\Controllers\RecordPreparation\Firm\RecordOfClient($firm, '1');
        $this->clientTwo = new \Tests\Controllers\RecordPreparation\Firm\RecordOfClient($firm, '2');
        
        $this->teamOne = new \Tests\Controllers\RecordPreparation\Firm\RecordOfTeam($firm, $this->clientOne, '1');
        $this->teamTwo = new \Tests\Controllers\RecordPreparation\Firm\RecordOfTeam($firm, null, '2');
        
        $this->member_11_t1 = new \Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember($this->teamOne, $this->clientOne, '11');
        $this->member_12_t1 = new \Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember($this->teamOne, $this->clientTwo, '12');
        
        $this->addRequest = [
            'name' => 'new team name',
            'members' => [
                [
                    'clientId' => $this->clientOne->id,
                    'position' => 'member one position',
                ],
                [
                    'clientId' => $this->clientTwo->id,
                    'position' => 'member two position',
                ],
            ],
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
    }
    
    protected function add()
    {
        $this->insertPreparedManagerRecord();
        
        $this->clientOne->insert($this->connection);
        $this->clientTwo->insert($this->connection);
        
        $uri = $this->managerUri . "/teams";
        $this->post($uri, $this->addRequest, $this->managerOne->token);
    }
    public function test_add_201()
    {
        $this->add();
        $this->seeStatusCode(201);
        
        $response = [
            'name' => $this->addRequest['name'],
        ];
        $this->seeJsonContains($response);
        
        $teamRecord = [
            'Firm_id' => $this->managerOne->firm->id,
            'name' => $this->addRequest['name'],
        ];
        $this->seeInDatabase('Team', $teamRecord);
    }
    public function test_add_aggregateMembers_201()
    {
        $this->add();
        $this->seeStatusCode(201);
        
        $memberOneResponse = [
            'position' => $this->addRequest['members'][0]['position'],
            'anAdmin' => true,
            'client' => [
                'id' => $this->clientOne->id,
                'name' => $this->clientOne->getFullName(),
            ],
        ];
        $this->seeJsonContains($memberOneResponse);
        
        $memberTwoResponse = [
            'position' => $this->addRequest['members'][1]['position'],
            'anAdmin' => true,
            'client' => [
                'id' => $this->clientTwo->id,
                'name' => $this->clientTwo->getFullName(),
            ],
        ];
        $this->seeJsonContains($memberTwoResponse);
        
        $memberOneRecord = [
            'position' => $this->addRequest['members'][0]['position'],
            'Client_id' => $this->clientOne->id,
            'anAdmin' => true,
            'active' => true,
        ];
        $this->seeInDatabase('T_Member', $memberOneRecord);
        
        $memberTwoRecord = [
            'position' => $this->addRequest['members'][1]['position'],
            'Client_id' => $this->clientTwo->id,
            'anAdmin' => true,
            'active' => true,
        ];
        $this->seeInDatabase('T_Member', $memberTwoRecord);
    }
    public function test_add_emptyName_400()
    {
        $this->addRequest['name'] = '';
        $this->add();
        $this->seeStatusCode(400);
    }
    public function test_add_emptyMember_403()
    {
        $this->addRequest['members'] = [];
        $this->add();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->insertPreparedManagerRecord();
        
        $this->member_11_t1->client->insert($this->connection);
        $this->member_12_t1->client->insert($this->connection);
        
        $this->teamOne->insert($this->connection);
        
        $this->member_11_t1->insert($this->connection);
        $this->member_12_t1->insert($this->connection);
        
        $uri = $this->managerUri . "/teams/{$this->teamOne->id}";
        $this->get($uri, $this->managerOne->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamOne->id,
            'name' => $this->teamOne->name,
            'createdTime' => $this->teamOne->createdTime,
            'creator' => [
                'id' => $this->teamOne->creator->id,
                'name' => $this->teamOne->creator->getFullName(),
            ],
            'members' => [
                [
                    'id' => $this->member_11_t1->id,
                    'position' => $this->member_11_t1->position,
                    'anAdmin' => $this->member_11_t1->anAdmin,
                    'joinTime' => $this->member_11_t1->joinTime,
                    'client' => [
                        'id' => $this->member_11_t1->client->id,
                        'name' => $this->member_11_t1->client->getFullName(),
                    ],
                ],
                [
                    'id' => $this->member_12_t1->id,
                    'position' => $this->member_12_t1->position,
                    'anAdmin' => $this->member_12_t1->anAdmin,
                    'joinTime' => $this->member_12_t1->joinTime,
                    'client' => [
                        'id' => $this->member_12_t1->client->id,
                        'name' => $this->member_12_t1->client->getFullName(),
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->insertPreparedManagerRecord();
        
        $this->member_11_t1->client->insert($this->connection);
        $this->member_12_t1->client->insert($this->connection);
        
        $this->teamOne->insert($this->connection);
        $this->teamTwo->insert($this->connection);
        
        $this->member_11_t1->insert($this->connection);
        $this->member_12_t1->insert($this->connection);
        
        $this->get($this->showAllUri, $this->managerOne->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->teamOne->id,
                    'name' => $this->teamOne->name,
                    'createdTime' => $this->teamOne->createdTime,
                    'creator' => [
                        'id' => $this->teamOne->creator->id,
                        'name' => $this->teamOne->creator->getFullName(),
                    ],
                ],
                [
                    'id' => $this->teamTwo->id,
                    'name' => $this->teamTwo->name,
                    'createdTime' => $this->teamTwo->createdTime,
                    'creator' => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_usingNameFilter_200()
    {
        $this->teamOne->name = 'awesome team';
        $this->showAllUri .= '?name=som';
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    'id' => $this->teamOne->id,
                    'name' => $this->teamOne->name,
                    'createdTime' => $this->teamOne->createdTime,
                    'creator' => [
                        'id' => $this->teamOne->creator->id,
                        'name' => $this->teamOne->creator->getFullName(),
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
