<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfIntegerFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfMultiSelectFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfSingleSelectFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfStringFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\BioSearchFilter\RecordOfTextAreaFieldSearchFilter;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioSearchFilter;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfMultiSelectField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfSelectField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfSingleSelectField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfTextAreaField;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class FirmControllerTest extends ClientTestCase
{
    protected $firmUri;
    protected $firmFileInfoLogo;
    
    protected $bioFormOne;
    protected $integerField_11;
    protected $stringField_11;
    protected $textAreaField_11;
    protected $singleSelectField_11;
    protected $multiSelectField_11;
    protected $bioFormTwo;
    protected $integerField_21;
    
    protected $bioSearchFilter;
    protected $integerFieldSearchFilterOne;
    protected $integerFieldSearchFilterTwo;
    protected $stringFieldSearchFilterOne;
    protected $textAreaFieldSearchFilterOne;
    protected $singleSelectFieldSearchFilterOne;
    protected $multiSelectFieldSearchFilterOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmUri = $this->clientUri . "/firm";
        
        $this->connection->table('Form')->truncate();
        $this->connection->table('BioForm')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('BioSearchFilter')->truncate();
        $this->connection->table('IntegerFieldSearchFilter')->truncate();
        $this->connection->table('StringFieldSearchFilter')->truncate();
        $this->connection->table('TextAreaFieldSearchFilter')->truncate();
        $this->connection->table('SingleSelectFieldSearchFilter')->truncate();
        $this->connection->table('MultiSelectFieldSearchFilter')->truncate();
        
        $firm = $this->client->firm;
        
        $formOne = new RecordOfForm('1');
        $formTwo = new RecordOfForm('2');
        
        $this->bioFormOne = new RecordOfBioForm($firm, $formOne);
        $this->bioFormTwo = new RecordOfBioForm($firm, $formTwo);
        
        $this->integerField_11 = new RecordOfIntegerField($formOne, '11');
        $this->integerField_21 = new RecordOfIntegerField($formTwo, '21');
        
        $this->stringField_11 = new RecordOfStringField($formOne, '11');
        
        $this->textAreaField_11 = new RecordOfTextAreaField($formOne, '11');
        
        $selectField_single_11 = new RecordOfSelectField('single-11');
        $selectField_multi_11 = new RecordOfSelectField('multi-11');
        
        $this->singleSelectField_11 = new RecordOfSingleSelectField($formOne, $selectField_single_11);
        
        $this->multiSelectField_11 = new RecordOfMultiSelectField($formOne, $selectField_multi_11);
        
        $this->bioSearchFilter = new RecordOfBioSearchFilter($firm);
        
        $this->integerFieldSearchFilterOne = new RecordOfIntegerFieldSearchFilter($this->bioSearchFilter, $this->integerField_11, '1');
        $this->integerFieldSearchFilterTwo = new RecordOfIntegerFieldSearchFilter($this->bioSearchFilter, $this->integerField_21, '2');
        
        $this->stringFieldSearchFilterOne = new RecordOfStringFieldSearchFilter($this->bioSearchFilter, $this->stringField_11, '1');
        
        $this->textAreaFieldSearchFilterOne = new RecordOfTextAreaFieldSearchFilter($this->bioSearchFilter, $this->textAreaField_11, '1');
        
        $this->singleSelectFieldSearchFilterOne = new RecordOfSingleSelectFieldSearchFilter($this->bioSearchFilter, $this->singleSelectField_11, '1');
        
        $this->multiSelectFieldSearchFilterOne = new RecordOfMultiSelectFieldSearchFilter($this->bioSearchFilter, $this->multiSelectField_11, '1');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('BioForm')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('BioSearchFilter')->truncate();
        $this->connection->table('IntegerFieldSearchFilter')->truncate();
        $this->connection->table('StringFieldSearchFilter')->truncate();
        $this->connection->table('TextAreaFieldSearchFilter')->truncate();
        $this->connection->table('SingleSelectFieldSearchFilter')->truncate();
        $this->connection->table('MultiSelectFieldSearchFilter')->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "name" => $this->client->firm->name,
            "domain" => $this->client->firm->url,
            "mailSenderAddress" => $this->client->firm->mailSenderAddress,
            "mailSenderName" => $this->client->firm->mailSenderName,
            "logo" => null,
            "displaySetting" => $this->client->firm->displaySetting,
        ];
        $this->firmUri .= "/info";
        $this->get($this->firmUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    protected function executeShowBioSearchFilter()
    {
        $this->bioFormOne->insert($this->connection);
        $this->bioFormTwo->insert($this->connection);
        $this->integerField_11->insert($this->connection);
        $this->integerField_21->insert($this->connection);
        $this->stringField_11->insert($this->connection);
        $this->textAreaField_11->insert($this->connection);
        $this->singleSelectField_11->insert($this->connection);
        $this->multiSelectField_11->insert($this->connection);
        
        $this->bioSearchFilter->insert($this->connection);
        $this->integerFieldSearchFilterOne->insert($this->connection);
        $this->integerFieldSearchFilterTwo->insert($this->connection);
        $this->stringFieldSearchFilterOne->insert($this->connection);
        $this->textAreaFieldSearchFilterOne->insert($this->connection);
        $this->singleSelectFieldSearchFilterOne->insert($this->connection);
        $this->multiSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->firmUri .= "/bio-search-filter";
        $this->get($this->firmUri, $this->client->token);
    }
    public function test_showBioSearchFilter_200()
    {
        $this->executeShowBioSearchFilter();
        $this->seeStatusCode(200);
        
        $response = [
            'disabled' => $this->bioSearchFilter->disabled,
            'modifiedTime' => $this->bioSearchFilter->modifiedTime,
            'integerFieldSearchFilters' => [
                [
                    'disabled' => $this->integerFieldSearchFilterOne->disabled,
                    'comparisonType' => $this->integerFieldSearchFilterOne->comparisonType,
                    'comparisonTypeDisplayValue' => 'EQUALS',
                    'integerField' => [
                        'id' => $this->integerFieldSearchFilterOne->integerField->id,
                        'name' => $this->integerFieldSearchFilterOne->integerField->name,
                    ],
                ],
                [
                    'disabled' => $this->integerFieldSearchFilterTwo->disabled,
                    'comparisonType' => $this->integerFieldSearchFilterTwo->comparisonType,
                    'comparisonTypeDisplayValue' => 'EQUALS',
                    'integerField' => [
                        'id' => $this->integerFieldSearchFilterTwo->integerField->id,
                        'name' => $this->integerFieldSearchFilterTwo->integerField->name,
                    ],
                ],
            ],
            'stringFieldSearchFilters' => [
                [
                    'disabled' => $this->stringFieldSearchFilterOne->disabled,
                    'comparisonType' => $this->stringFieldSearchFilterOne->comparisonType,
                    'comparisonTypeDisplayValue' => 'EQUALS',
                    'stringField' => [
                        'id' => $this->stringFieldSearchFilterOne->stringField->id,
                        'name' => $this->stringFieldSearchFilterOne->stringField->name,
                    ],
                ],
            ],
            'textAreaFieldSearchFilters' => [
                [
                    'disabled' => $this->textAreaFieldSearchFilterOne->disabled,
                    'comparisonType' => $this->textAreaFieldSearchFilterOne->comparisonType,
                    'comparisonTypeDisplayValue' => 'EQUALS',
                    'textAreaField' => [
                        'id' => $this->textAreaFieldSearchFilterOne->textAreaField->id,
                        'name' => $this->textAreaFieldSearchFilterOne->textAreaField->name,
                    ],
                ],
            ],
            'singleSelectFieldSearchFilters' => [
                [
                    'disabled' => $this->singleSelectFieldSearchFilterOne->disabled,
                    'comparisonType' => $this->singleSelectFieldSearchFilterOne->comparisonType,
                    'comparisonTypeDisplayValue' => 'IN',
                    'singleSelectField' => [
                        'id' => $this->singleSelectFieldSearchFilterOne->singleSelectField->id,
                        'name' => $this->singleSelectFieldSearchFilterOne->singleSelectField->selectField->name,
                    ],
                ],
                
            ],
            'multiSelectFieldSearchFilters' => [
                [
                    'disabled' => $this->multiSelectFieldSearchFilterOne->disabled,
                    'comparisonType' => $this->multiSelectFieldSearchFilterOne->comparisonType,
                    'comparisonTypeDisplayValue' => 'IN',
                    'multiSelectField' => [
                        'id' => $this->multiSelectFieldSearchFilterOne->multiSelectField->id,
                        'name' => $this->multiSelectFieldSearchFilterOne->multiSelectField->selectField->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showBioSearchFilter_noBioSearchFilterSet_200()
    {
        $this->firmUri .= "/bio-search-filter";
        $this->get($this->firmUri, $this->client->token);
        $this->seeStatusCode(200);
        $response = [
            'data' => null,
            'meta' => [
                'code' => 200,
                'type' => 'OK'
            ]
        ];
    }
}
