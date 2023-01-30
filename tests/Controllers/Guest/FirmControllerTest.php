<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\RecordPreparation\RecordOfFirm;

class FirmControllerTest extends \Tests\Controllers\ControllerTestCase
{
    protected $firmUri = '/api/v1/firms';
    protected $firmOne;
    protected $firmTwo;
    
    protected $managerOneA;
    protected $managerOneB;
    protected $managerTwo;
    //
    protected $addFirmRequest = [
        'name' => 'new firm name',
        'identifier' => 'new_firm',
        'sharingPercentage' => 2.5,
        'whitelableInfo' => [
            'url' => 'https://new-firm.org',
            'mailSenderAddress' => 'noreply@firm.org',
            'mailSenderName' => 'new firm team',
        ],
        'managers' => [
            [
                'name' => 'new manager 1 name',
                'email' => 'newManager1@newFirm.org',
                'password' => 'iousehr12312k3j',
                'phone' => '081323211233',
            ],
            [
                'name' => 'new manager 2 name',
                'email' => 'newManager2@newFirm.org',
                'password' => 'iousehr12312k3j',
                'phone' => '081323211235',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
        
        $this->firmOne = new RecordOfFirm(1);
        $this->firmTwo = new RecordOfFirm(2);
        
        $this->managerOneA = new \Tests\Controllers\RecordPreparation\Firm\RecordOfManager($this->firmOne, '1a');
        $this->managerOneB = new \Tests\Controllers\RecordPreparation\Firm\RecordOfManager($this->firmOne, '1b');
        $this->managerTwo = new \Tests\Controllers\RecordPreparation\Firm\RecordOfManager($this->firmTwo, 2);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
    }
    
    protected function add()
    {
        $this->post($this->firmUri, $this->addFirmRequest);
//echo $this->firmUri;
//echo json_encode($this->addFirmRequest);
//$this->seeJsonContains(['print']);
    }
    public function test_add_201()
    {
$this->disableExceptionHandling();
        $this->add();
        $this->seeStatusCode(201);
        
        $firmResponse = [
            'name' => $this->addFirmRequest['name'],
            'identifier' => $this->addFirmRequest['identifier'],
            'sharingPercentage' => $this->addFirmRequest['sharingPercentage'],
            'whitelableInfo' => [
                'url' => $this->addFirmRequest['whitelableInfo']['url'],
                'mailSenderAddress' => $this->addFirmRequest['whitelableInfo']['mailSenderAddress'],
                'mailSenderName' => $this->addFirmRequest['whitelableInfo']['mailSenderName'],
            ],
        ];
        $this->seeJsonContains($firmResponse);
        
        $firmEntry = [
            'name' => $this->addFirmRequest['name'],
            'identifier' => $this->addFirmRequest['identifier'],
            'sharingPercentage' => $this->addFirmRequest['sharingPercentage'],
            'url' => $this->addFirmRequest['whitelableInfo']['url'],
            'mailSenderAddress' => $this->addFirmRequest['whitelableInfo']['mailSenderAddress'],
            'mailSenderName' => $this->addFirmRequest['whitelableInfo']['mailSenderName'],
        ];
        $this->seeInDatabase('Firm', $firmEntry);
    }
    public function test_add_persistManager()
    {
        $this->add();
        $this->seeStatusCode(201);
        
        $managerResponse = [
            'name' => $this->addFirmRequest['managers'][0]['name'],
            'email' => $this->addFirmRequest['managers'][0]['email'],
            'phone' => $this->addFirmRequest['managers'][0]['phone'],
        ];
        $this->seeJsonContains($managerResponse);
        
        $managerResponse = [
            'name' => $this->addFirmRequest['managers'][1]['name'],
            'email' => $this->addFirmRequest['managers'][1]['email'],
            'phone' => $this->addFirmRequest['managers'][1]['phone'],
        ];
        $this->seeJsonContains($managerResponse);
        
        $managerEntry = [
            'name' => $this->addFirmRequest['managers'][0]['name'],
            'email' => $this->addFirmRequest['managers'][0]['email'],
            'phone' => $this->addFirmRequest['managers'][0]['phone'],
        ];
        $this->seeInDatabase('Manager', $managerEntry);
        
        $managerEntry = [
            'name' => $this->addFirmRequest['managers'][1]['name'],
            'email' => $this->addFirmRequest['managers'][1]['email'],
            'phone' => $this->addFirmRequest['managers'][1]['phone'],
        ];
        $this->seeInDatabase('Manager', $managerEntry);
    }
    
    protected function viewList()
    {
        $this->firmOne->insert($this->connection);
        $this->firmTwo->insert($this->connection);
        
        $this->get($this->firmUri);
//echo $this->firmUri;
//$this->seeJsonContains(['print']);
    }
    public function test_viewList_200()
    {
        $this->viewList();
        $this->seeStatusCode(200);
        
        $response = [
            'list' => [
                [
                    'id' => $this->firmOne->id,
                    'name' => $this->firmOne->name,
                ],
                [
                    'id' => $this->firmTwo->id,
                    'name' => $this->firmTwo->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function viewDetail()
    {
        $this->firmOne->insert($this->connection);
        $this->managerOneA->insert($this->connection);
        $this->managerOneB->insert($this->connection);
        
        $uri = $this->firmUri . "/{$this->firmOne->id}";
        $this->get($uri);
//echo $uri;
//$this->seeJsonContains(['print']);
    }
    public function test_viewDetail_201()
    {
        $this->viewDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->firmOne->id,
            'name' => $this->firmOne->name,
            'identifier' => $this->firmOne->identifier,
            'sharingPercentage' => $this->firmOne->sharingPercentage,
            'managers' => [
                [
                    'id' => $this->managerOneA->id,
                    'name' => $this->managerOneA->name,
                    'email' => $this->managerOneA->email,
                    'phone' => $this->managerOneA->phone,
                ],
                [
                    'id' => $this->managerOneB->id,
                    'name' => $this->managerOneB->name,
                    'email' => $this->managerOneB->email,
                    'phone' => $this->managerOneB->phone,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
