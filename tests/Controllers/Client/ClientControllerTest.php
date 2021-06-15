<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfIntegerFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfMultiSelectFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfSingleSelectFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfStringFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfTextAreaFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientBio;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfMultiSelectField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfSelectField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfSingleSelectField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfTextAreaField;
use Tests\Controllers\RecordPreparation\Shared\Form\SelectField\RecordOfOption;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\MultiSelectFieldRecord\RecordOfSelectedOption;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfIntegerFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfMultiSelectFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfSingleSelectFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfTextAreaFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ClientControllerTest extends ClientTestCase
{
    protected $clientOne;
    
    protected $bioFormOne;
    protected $bioFormTwo;
    
    protected $integerField_11;
    protected $integerField_21;
    protected $stringField_11;
    protected $textAreaField_11;
    protected $singleSelectField_11;
    protected $multiSelectField_11;
    
    protected $option_11;
    protected $option_12;
    protected $option_21;
    protected $option_22;
    
    protected $bioSearchFilter;
    protected $integerFieldSearchFilterOne;
    protected $integerFieldSearchFilterTwo;
    protected $stringFieldSearchFilterOne;
    protected $textAreaFieldSearchFilterOne;
    protected $singleSelectFieldSearchFilterOne;
    protected $multiSelectFieldSearchFilterOne;

    protected $clientBio_11;
    protected $clientBio_12;
    
    protected $integerFieldRecord_111;
    protected $integerFieldRecord_121;
    protected $stringFieldRecord_111;
    protected $textAreaFieldRecord_111;
    protected $singleSelectFieldRecord_111;
    protected $multiSelectFieldRecord_111;
    
    protected $selectedOption_1111;
    
    protected $searchRequest;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->clientUri .= "/clients";
        
        $this->connection->table('Form')->truncate();
        $this->connection->table('BioForm')->truncate();
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('BioSearchFilter')->truncate();
        $this->connection->table('IntegerFieldSearchFilter')->truncate();
        $this->connection->table('StringFieldSearchFilter')->truncate();
        $this->connection->table('TextAreaFieldSearchFilter')->truncate();
        $this->connection->table('SingleSelectFieldSearchFilter')->truncate();
        $this->connection->table('MultiSelectFieldSearchFilter')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ClientBio')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        $this->connection->table('SingleSelectFieldRecord')->truncate();
        $this->connection->table('MultiSelectFieldRecord')->truncate();
        $this->connection->table('SelectedOption')->truncate();
        
        $firm = $this->client->firm;
        
        $this->clientOne = new RecordOfClient($firm, '1');
        
        $formOne = new RecordOfForm('1');
        $formTwo = new RecordOfForm('2');
        
        $this->bioFormOne = new RecordOfBioForm($firm, $formOne);
        $this->bioFormTwo = new RecordOfBioForm($firm, $formTwo);
        
        $selectFieldOne = new RecordOfSelectField('1');
        $selectFieldTwo = new RecordOfSelectField('2');
        
        $this->option_11 = new RecordOfOption($selectFieldOne, '11');
        $this->option_12 = new RecordOfOption($selectFieldOne, '12');
        $this->option_21 = new RecordOfOption($selectFieldTwo, '21');
        $this->option_22 = new RecordOfOption($selectFieldTwo, '22');
        
        $this->integerField_11 = new RecordOfIntegerField($formOne, '11');
        $this->integerField_21 = new RecordOfIntegerField($formTwo, '21');
        $this->stringField_11 = new RecordOfStringField($formOne, '11');
        $this->textAreaField_11 = new RecordOfTextAreaField($formOne, '11');
        $this->singleSelectField_11 = new RecordOfSingleSelectField($formOne, $selectFieldOne);
        $this->multiSelectField_11 = new RecordOfMultiSelectField($formOne, $selectFieldTwo);
        
        $this->bioSearchFilter = new RecordOfBioSearchFilter($firm);
        $this->integerFieldSearchFilterOne = new RecordOfIntegerFieldSearchFilter($this->bioSearchFilter, $this->integerField_11, '1');
        $this->integerFieldSearchFilterTwo = new RecordOfIntegerFieldSearchFilter($this->bioSearchFilter, $this->integerField_21, '2');
        $this->stringFieldSearchFilterOne = new RecordOfStringFieldSearchFilter($this->bioSearchFilter, $this->stringField_11, '1');
        $this->textAreaFieldSearchFilterOne = new RecordOfTextAreaFieldSearchFilter($this->bioSearchFilter, $this->textAreaField_11, '1');
        $this->singleSelectFieldSearchFilterOne = new RecordOfSingleSelectFieldSearchFilter($this->bioSearchFilter, $this->singleSelectField_11, '1');
        $this->multiSelectFieldSearchFilterOne = new RecordOfMultiSelectFieldSearchFilter($this->bioSearchFilter, $this->multiSelectField_11, '1');
        
        $formRecord_11 = new RecordOfFormRecord($formOne, '11');
        $formRecord_12 = new RecordOfFormRecord($formTwo, '12');
        
        $this->clientBio_11 = new RecordOfClientBio($this->clientOne, $this->bioFormOne, $formRecord_11);
        $this->clientBio_12= new RecordOfClientBio($this->clientOne, $this->bioFormTwo, $formRecord_12);
        
        $this->integerFieldRecord_111 = new RecordOfIntegerFieldRecord($formRecord_11, $this->integerField_11, '111');
        $this->integerFieldRecord_111->value = 111;
        $this->integerFieldRecord_121 = new RecordOfIntegerFieldRecord($formRecord_12, $this->integerField_21, '121');
        $this->integerFieldRecord_121->value = 121;
        $this->stringFieldRecord_111 = new RecordOfStringFieldRecord($formRecord_11, $this->stringField_11, '111');
        $this->stringFieldRecord_111->value = "string-one";
        $this->textAreaFieldRecord_111 = new RecordOfTextAreaFieldRecord($formRecord_11, $this->textAreaField_11, '111');
        $this->textAreaFieldRecord_111->value = "textArea-one";
        $this->singleSelectFieldRecord_111 = new RecordOfSingleSelectFieldRecord($formRecord_11, $this->singleSelectField_11, $this->option_11, '111');
        $this->multiSelectFieldRecord_111 = new RecordOfMultiSelectFieldRecord($formRecord_11, $this->multiSelectField_11, '111');
        
        $this->selectedOption_1111 = new RecordOfSelectedOption($this->multiSelectFieldRecord_111, $this->option_21, '1111');
        
        $this->searchRequest = [
            'integerFieldFilters' => [],
            'stringFieldFilters' => [],
            'textAreaFieldFilters' => [],
            'singleSelectFieldFilters' => [],
            'multiSelectFieldFilters' => [],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('BioForm')->truncate();
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('BioSearchFilter')->truncate();
        $this->connection->table('IntegerFieldSearchFilter')->truncate();
        $this->connection->table('StringFieldSearchFilter')->truncate();
        $this->connection->table('TextAreaFieldSearchFilter')->truncate();
        $this->connection->table('SingleSelectFieldSearchFilter')->truncate();
        $this->connection->table('MultiSelectFieldSearchFilter')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ClientBio')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        $this->connection->table('SingleSelectFieldRecord')->truncate();
        $this->connection->table('MultiSelectFieldRecord')->truncate();
        $this->connection->table('SelectedOption')->truncate();
    }
    
    protected function executeShowAll()
    {
        $this->bioFormOne->insert($this->connection);
        $this->bioFormTwo->insert($this->connection);
        
        $this->bioSearchFilter->insert($this->connection);
        
        $this->clientOne->insert($this->connection);
        
        $this->clientBio_11->insert($this->connection);
        $this->clientBio_12->insert($this->connection);
        
        $this->get($this->clientUri, $this->client->token);
    }
    
    protected function executeShowAll_integerField()
    {
        $this->integerField_11->insert($this->connection);
        $this->integerField_21->insert($this->connection);
        
        $this->integerFieldSearchFilterOne->insert($this->connection);
        $this->integerFieldSearchFilterTwo->insert($this->connection);
        
        $this->integerFieldRecord_111->insert($this->connection);
        $this->integerFieldRecord_121->insert($this->connection);
        
        $this->searchRequest['integerFieldFilters'] = [
            [
                'id' => $this->integerField_11->id,
                'value' => $this->integerFieldRecord_111->value,
            ],
            [
                'id' => $this->integerField_21->id,
                'value' => $this->integerFieldRecord_121->value,
            ],
        ];
        $filters = json_encode($this->searchRequest);
        $this->clientUri .= "?page=1&pageSize=10&filters={$filters}";
        $this->executeShowAll();
    }
    public function test_executeShowAll_integerContext_integerField_200()
    {
        $this->executeShowAll_integerField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_integerContext_removedMatchClientBio_200()
    {
        $this->clientBio_11->removed = true;
        $this->clientBio_12->removed = true;
        
        $this->executeShowAll_integerField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_integerContext_removedMatchIntegerField_200()
    {
        $this->integerFieldRecord_111->removed = true;
        $this->integerFieldRecord_121->removed = true;
        
        $this->executeShowAll_integerField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_integerContext_matchClientFromDifferentFirm_200()
    {
        $otherFirm = new RecordOfFirm('other');
        $otherFirm->insert($this->connection);
        
        $this->clientOne->firm = $otherFirm;
        
        $this->executeShowAll_integerField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_integerContext_otherMatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $integerFieldRecord_211 = new RecordOfIntegerFieldRecord($clientBio_21->formRecord, $this->integerField_11, '211');
        $integerFieldRecord_211->value = $this->integerFieldRecord_111->value;
        $integerFieldRecord_211->insert($this->connection);
        
        $this->executeShowAll_integerField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '2',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
                [
                    'id' => $clientTwo->id,
                    'firstName' => $clientTwo->firstName,
                    'lastName' => $clientTwo->lastName,
                    'email' => $clientTwo->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_integerContext_otherUnmatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $integerFieldRecord_211 = new RecordOfIntegerFieldRecord($clientBio_21->formRecord, $this->integerField_11, '211');
        $integerFieldRecord_211->insert($this->connection);
        
        $this->executeShowAll_integerField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeShowAll_stringField()
    {
        $this->stringField_11->insert($this->connection);
        
        $this->stringFieldSearchFilterOne->insert($this->connection);
        
        $this->stringFieldRecord_111->insert($this->connection);
        
        $this->searchRequest['stringFieldFilters'] = [
            [
                'id' => $this->stringField_11->id,
                'value' => $this->stringFieldRecord_111->value,
            ],
        ];
        $filters = json_encode($this->searchRequest);
        $this->clientUri .= "?page=1&pageSize=10&filters={$filters}";
        $this->executeShowAll();
    }
    public function test_executeShowAll_stringField_200()
    {
        $this->executeShowAll_stringField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_stringContext_removedMatchClientBio_200()
    {
        $this->clientBio_11->removed = true;
        $this->clientBio_12->removed = true;
        
        $this->executeShowAll_stringField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_stringContext_removedMatchStringField_200()
    {
        $this->stringFieldRecord_111->removed = true;
        
        $this->executeShowAll_stringField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_stringContext_matchClientFromDifferentFirm_200()
    {
        $otherFirm = new RecordOfFirm('other');
        $otherFirm->insert($this->connection);
        
        $this->clientOne->firm = $otherFirm;
        
        $this->executeShowAll_stringField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_stringContext_otherMatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $stringFieldRecord_211 = new RecordOfStringFieldRecord($clientBio_21->formRecord, $this->stringField_11, '211');
        $stringFieldRecord_211->value = $this->stringFieldRecord_111->value;
        $stringFieldRecord_211->insert($this->connection);
        
        $this->executeShowAll_stringField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '2',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
                [
                    'id' => $clientTwo->id,
                    'firstName' => $clientTwo->firstName,
                    'lastName' => $clientTwo->lastName,
                    'email' => $clientTwo->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_stringContext_otherUnmatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $stringFieldRecord_211 = new RecordOfStringFieldRecord($clientBio_21->formRecord, $this->stringField_11, '211');
        $stringFieldRecord_211->insert($this->connection);
        
        $this->executeShowAll_stringField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeShowAll_textAreaField()
    {
        $this->textAreaField_11->insert($this->connection);
        
        $this->textAreaFieldSearchFilterOne->insert($this->connection);
        
        $this->textAreaFieldRecord_111->insert($this->connection);
        
        $this->searchRequest['textAreaFieldFilters'] = [
            [
                'id' => $this->textAreaField_11->id,
                'value' => $this->textAreaFieldRecord_111->value,
            ],
        ];
        $filters = json_encode($this->searchRequest);
        $this->clientUri .= "?page=1&pageSize=10&filters={$filters}";
        $this->executeShowAll();
    }
    public function test_executeShowAll_textAreaField_200()
    {
        $this->executeShowAll_textAreaField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_textAreaContext_removedMatchClientBio_200()
    {
        $this->clientBio_11->removed = true;
        $this->clientBio_12->removed = true;
        
        $this->executeShowAll_textAreaField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_textAreaContext_removedMatchTextAreaField_200()
    {
        $this->textAreaFieldRecord_111->removed = true;
        
        $this->executeShowAll_textAreaField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_textAreaContext_matchClientFromDifferentFirm_200()
    {
        $otherFirm = new RecordOfFirm('other');
        $otherFirm->insert($this->connection);
        
        $this->clientOne->firm = $otherFirm;
        
        $this->executeShowAll_textAreaField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_textAreaContext_otherMatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $textAreaFieldRecord_211 = new RecordOfTextAreaFieldRecord($clientBio_21->formRecord, $this->textAreaField_11, '211');
        $textAreaFieldRecord_211->value = $this->textAreaFieldRecord_111->value;
        $textAreaFieldRecord_211->insert($this->connection);
        
        $this->executeShowAll_textAreaField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '2',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
                [
                    'id' => $clientTwo->id,
                    'firstName' => $clientTwo->firstName,
                    'lastName' => $clientTwo->lastName,
                    'email' => $clientTwo->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_textAreaContext_otherUnmatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $textAreaFieldRecord_211 = new RecordOfTextAreaFieldRecord($clientBio_21->formRecord, $this->textAreaField_11, '211');
        $textAreaFieldRecord_211->insert($this->connection);
        
        $this->executeShowAll_textAreaField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeShowAll_singleSelectField()
    {
        $this->singleSelectField_11->insert($this->connection);
        
        $this->singleSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->singleSelectFieldRecord_111->insert($this->connection);
        
        $this->searchRequest['singleSelectFieldFilters'] = [
            [
                'id' => $this->singleSelectField_11->id,
                'listOfOptionId' => [
                    $this->option_11->id,
                ],
            ],
        ];
        $filters = json_encode($this->searchRequest);
        $this->clientUri .= "?page=1&pageSize=10&filters={$filters}";
        $this->executeShowAll();
    }
    public function test_executeShowAll_singleSelectField_200()
    {
        $this->executeShowAll_singleSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_singleSelectContext_removedMatchClientBio_200()
    {
        $this->clientBio_11->removed = true;
        $this->clientBio_12->removed = true;
        
        $this->executeShowAll_singleSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_singleSelectContext_removedMatchSingleSelectField_200()
    {
        $this->singleSelectFieldRecord_111->removed = true;
        
        $this->executeShowAll_singleSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_singleSelectContext_matchClientFromDifferentFirm_200()
    {
        $otherFirm = new RecordOfFirm('other');
        $otherFirm->insert($this->connection);
        
        $this->clientOne->firm = $otherFirm;
        
        $this->executeShowAll_singleSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_singleSelectContext_otherMatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $singleSelectFieldRecord_211 = new RecordOfSingleSelectFieldRecord($formRecord_21, $this->singleSelectField_11, $this->option_11, '211');
        $singleSelectFieldRecord_211->insert($this->connection);
        
        $this->executeShowAll_singleSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '2',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
                [
                    'id' => $clientTwo->id,
                    'firstName' => $clientTwo->firstName,
                    'lastName' => $clientTwo->lastName,
                    'email' => $clientTwo->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_singleSelectContext_otherUnmatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $singleSelectFieldRecord_211 = new RecordOfSingleSelectFieldRecord($formRecord_21, $this->singleSelectField_11, $this->option_12, '211');
        $singleSelectFieldRecord_211->insert($this->connection);
        
        $this->executeShowAll_singleSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeShowAll_multiSelectField()
    {
        $this->multiSelectField_11->insert($this->connection);
        
        $this->multiSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->multiSelectFieldRecord_111->insert($this->connection);
        
        $this->selectedOption_1111->insert($this->connection);
        
        $this->searchRequest['multiSelectFieldFilters'] = [
            [
                'id' => $this->multiSelectField_11->id,
                'listOfOptionId' => [
                    $this->option_21->id,
                ],
            ],
        ];
        $filters = json_encode($this->searchRequest);
        $this->clientUri .= "?page=1&pageSize=10&filters={$filters}";
        $this->executeShowAll();
    }
    public function test_executeShowAll_multiSelectField_200()
    {
        $this->executeShowAll_multiSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_multiSelectContext_removedMatchClientBio_200()
    {
        $this->clientBio_11->removed = true;
        $this->clientBio_12->removed = true;
        
        $this->executeShowAll_multiSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_multiSelectContext_removedMatchMultiSelectField_200()
    {
        $this->multiSelectFieldRecord_111->removed = true;
        
        $this->executeShowAll_multiSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_multiSelectContext_removedMatchSelectedOption_200()
    {
        $this->selectedOption_1111->removed = true;
        
        $this->executeShowAll_multiSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_multiSelectContext_matchClientFromDifferentFirm_200()
    {
        $otherFirm = new RecordOfFirm('other');
        $otherFirm->insert($this->connection);
        
        $this->clientOne->firm = $otherFirm;
        
        $this->executeShowAll_multiSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '0',
            'list' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_multiSelectContext_otherMatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $multiSelectFieldRecord_211 = new RecordOfMultiSelectFieldRecord($formRecord_21, $this->multiSelectField_11, '211');
        $multiSelectFieldRecord_211->insert($this->connection);
        
        $selectedOption_2111 = new RecordOfSelectedOption($multiSelectFieldRecord_211, $this->option_21, '2111');
        $selectedOption_2111->insert($this->connection);
        
        $this->executeShowAll_multiSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '2',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
                [
                    'id' => $clientTwo->id,
                    'firstName' => $clientTwo->firstName,
                    'lastName' => $clientTwo->lastName,
                    'email' => $clientTwo->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_executeShowAll_multiSelectContext_otherUnmatchClient_200()
    {
        $clientTwo = new RecordOfClient($this->client->firm, '2');
        $clientTwo->insert($this->connection);
        
        $formRecord_21 = new RecordOfFormRecord($this->bioFormOne->form, '21');
        $clientBio_21 = new RecordOfClientBio($clientTwo, $this->bioFormOne, $formRecord_21);
        $clientBio_21->insert($this->connection);
        
        $multiSelectFieldRecord_211 = new RecordOfMultiSelectFieldRecord($formRecord_21, $this->multiSelectField_11, '211');
        $multiSelectFieldRecord_211->insert($this->connection);
        
        $selectedOption_2111 = new RecordOfSelectedOption($multiSelectFieldRecord_211, $this->option_22, '2111');
        $selectedOption_2111->insert($this->connection);
        
        $this->executeShowAll_multiSelectField();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '1',
            'list' => [
                [
                    'id' => $this->clientOne->id,
                    'firstName' => $this->clientOne->firstName,
                    'lastName' => $this->clientOne->lastName,
                    'email' => $this->clientOne->email,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
/*
    public function test_showAll_200()
    {
        $this->searchRequest = [
            'integerFieldFilters' => [
                [
                    'id' => $this->integerField_11->id,
                    'value' => $this->integerFieldRecord_111->value,
                ],
                [
                    'id' => $this->integerField_21->id,
                    'value' => $this->integerFieldRecord_121->value,
                ],
            ],
            'stringFieldFilters' => [
                [
                    'id' => $this->stringField_11->id,
                    'value' => $this->stringFieldRecord_111->value,
                ],
            ],
            'textAreaFieldFilters' => [
                [
                    'id' => $this->textAreaField_11->id,
                    'value' => $this->textAreaFieldRecord_111->value,
                ],
            ],
            'singleSelectFieldFilters' => [
                [
                    'id' => $this->singleSelectField_11->id,
                    'listOfOptionId' => [$this->option_11->id],
                ],
            ],
            'multiSelectFieldFilters' => [
                [
                    'id' => $this->multiSelectField_11->id,
                    'listOfOptionId' => [$this->option_21->id, $this->option_22->id],
                ],
            ],
        ];
        $this->clientUri .= "?page=1&pageSize=10"
                . "integerFieldFilters[]=[]"
    }
 * 
 */
}
