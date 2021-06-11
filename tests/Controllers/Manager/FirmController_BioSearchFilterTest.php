<?php

namespace Tests\Controllers\Manager;

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

class FirmController_BioSearchFilterTest extends ManagerTestCase
{
    protected $setBioFormFilterUri;
    
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
    protected $stringFieldSearchFilterOne;
    protected $textAreaFieldSearchFilterOne;
    protected $singleSelectFieldSearchFilterOne;
    protected $multiSelectFieldSearchFilterOne;
    
    protected $bioSearchFilterRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setBioFormFilterUri = $this->managerUri . "/firm-profile/bio-search-filter";
        
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
        
        $firm = $this->manager->firm;
        
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
        
        $this->stringFieldSearchFilterOne = new RecordOfStringFieldSearchFilter($this->bioSearchFilter, $this->stringField_11, '1');
        
        $this->textAreaFieldSearchFilterOne = new RecordOfTextAreaFieldSearchFilter($this->bioSearchFilter, $this->textAreaField_11, '1');
        
        $this->singleSelectFieldSearchFilterOne = new RecordOfSingleSelectFieldSearchFilter($this->bioSearchFilter, $this->singleSelectField_11, '1');
        
        $this->multiSelectFieldSearchFilterOne = new RecordOfMultiSelectFieldSearchFilter($this->bioSearchFilter, $this->multiSelectField_11, '1');
        
        $this->bioSearchFilterRequest = [
            'bioForms' => [
                [
                    'id' => $this->bioFormOne->form->id,
                    'integerFields' => [],
                    'stringFields' => [],
                    'textAreaFields' => [],
                    'singleSelectFields' => [],
                    'multiSelectFields' => [],
                ],
            ],
        ];
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
    
    protected function executeBioSearchFilter_integerField()
    {
        $this->bioFormOne->insert($this->connection);
        $this->integerField_11->insert($this->connection);
        
        $this->bioSearchFilterRequest['bioForms'][0]['integerFields'][] = [
            'id' => $this->integerField_11->id,
            'comparisonType' => 2,
        ];
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
    }
    public function test_containNonExistingIntegerFieldFilter_addNewIntegerFieldSearchFilter()
    {
        $this->bioFormTwo->insert($this->connection);
        $this->integerField_21->insert($this->connection);
        $this->bioSearchFilterRequest['bioForms'][1] = [
            'id' => $this->bioFormTwo->form->id,
            'integerFields' => [
                [
                    'id' => $this->integerField_21->id,
                    'comparisonType' => 1,
                ],
            ],
            'stringFields' => [],
            'textAreaFields' => [],
            'singleSelectFields' => [],
            'multiSelectFields' => [],
        ];
        
        $this->executeBioSearchFilter_integerField();
        $this->seeStatusCode(200);
        
        $integerFieldSearchFilterOneResponse = [
            'integerField' => [
                'id' => $this->integerField_11->id,
                'name' => $this->integerField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($integerFieldSearchFilterOneResponse);
        
        $integerFieldSearchFilterTwoResponse = [
            'integerField' => [
                'id' => $this->integerField_21->id,
                'name' => $this->integerField_21->name,
            ],
            'disabled' => false,
            'comparisonType' => 1,
        ];
        $this->seeJsonContains($integerFieldSearchFilterTwoResponse);
        
        $integerFieldSearchFilterOneEntry = [
            'IntegerField_id' => $this->integerField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('IntegerFieldSearchFilter', $integerFieldSearchFilterOneEntry);
        
        $integerFieldSearchFilterTwoEntry = [
            'IntegerField_id' => $this->integerField_21->id,
            'disabled' => false,
            'comparisonType' => 1,
        ];
        $this->seeInDatabase('IntegerFieldSearchFilter', $integerFieldSearchFilterTwoEntry);
    }
    public function test_containFilterCorrespondWithSameIntegerField_update()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->integerFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_integerField();
        $this->seeStatusCode(200);
        
        $integerFieldSearchFilterOneResponse = [
            'integerField' => [
                'id' => $this->integerField_11->id,
                'name' => $this->integerField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($integerFieldSearchFilterOneResponse);
        
        $integerFieldSearchFilterOneEntry = [
            'id' => $this->integerFieldSearchFilterOne->id,
            'IntegerField_id' => $this->integerField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('IntegerFieldSearchFilter', $integerFieldSearchFilterOneEntry);
    }
    public function test_containDisabledFilterCorrespondWithUpdatedIntegerField_enable()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->integerFieldSearchFilterOne->disabled = true;
        $this->integerFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_integerField();
        $this->seeStatusCode(200);
        
        $integerFieldSearchFilterOneResponse = [
            'integerField' => [
                'id' => $this->integerField_11->id,
                'name' => $this->integerField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($integerFieldSearchFilterOneResponse);
        
        $integerFieldSearchFilterOneEntry = [
            'id' => $this->integerFieldSearchFilterOne->id,
            'IntegerField_id' => $this->integerField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('IntegerFieldSearchFilter', $integerFieldSearchFilterOneEntry);
    }
    public function test_containIrrelevanFilter_NotCorrespondWithAnyIntegerField_disabled()
    {
        $this->bioFormOne->insert($this->connection);
        $this->integerField_11->insert($this->connection);
        
        $this->bioSearchFilter->insert($this->connection);
        $this->integerFieldSearchFilterOne->insert($this->connection);
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
        $this->seeStatusCode(200);
        
        $integerFieldSearchFilterOneEntry = [
            'id' => $this->integerFieldSearchFilterOne->id,
            'IntegerField_id' => $this->integerField_11->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('IntegerFieldSearchFilter', $integerFieldSearchFilterOneEntry);
    }
    public function test_integerFieldNotFoundInBioFormAggregate_404()
    {
        $this->bioFormTwo->insert($this->connection);
        $this->integerField_11->form = $this->bioFormTwo->form;
        
        $this->executeBioSearchFilter_integerField();
        $this->seeStatusCode(404);
    }
    
    protected function executeBioSearchFilter_stringField()
    {
        $this->bioFormOne->insert($this->connection);
        $this->stringField_11->insert($this->connection);
        
        $this->bioSearchFilterRequest['bioForms'][0]['stringFields'][] = [
            'id' => $this->stringField_11->id,
            'comparisonType' => 2,
        ];
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
    }
    public function test_containNonExistingStringFieldFilter_addNewStringFieldSearchFilter()
    {
        $this->executeBioSearchFilter_stringField();
        $this->seeStatusCode(200);
        
        $stringFieldSearchFilterOneResponse = [
            'stringField' => [
                'id' => $this->stringField_11->id,
                'name' => $this->stringField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($stringFieldSearchFilterOneResponse);
        
        $stringFieldSearchFilterOneEntry = [
            'StringField_id' => $this->stringField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('StringFieldSearchFilter', $stringFieldSearchFilterOneEntry);
    }
    public function test_containFilterCorrespondWithSameStringField_update()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->stringFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_stringField();
        $this->seeStatusCode(200);
        
        $stringFieldSearchFilterOneResponse = [
            'stringField' => [
                'id' => $this->stringField_11->id,
                'name' => $this->stringField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($stringFieldSearchFilterOneResponse);
        
        $stringFieldSearchFilterOneEntry = [
            'id' => $this->stringFieldSearchFilterOne->id,
            'StringField_id' => $this->stringField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('StringFieldSearchFilter', $stringFieldSearchFilterOneEntry);
    }
    public function test_containDisabledFilterCorrespondWithUpdatedStringField_enable()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->stringFieldSearchFilterOne->disabled = true;
        $this->stringFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_stringField();
        $this->seeStatusCode(200);
        
        $stringFieldSearchFilterOneResponse = [
            'stringField' => [
                'id' => $this->stringField_11->id,
                'name' => $this->stringField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($stringFieldSearchFilterOneResponse);
        
        $stringFieldSearchFilterOneEntry = [
            'id' => $this->stringFieldSearchFilterOne->id,
            'StringField_id' => $this->stringField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('StringFieldSearchFilter', $stringFieldSearchFilterOneEntry);
    }
    public function test_containIrrelevanFilter_NotCorrespondWithAnyStringField_disabled()
    {
        $this->bioFormOne->insert($this->connection);
        $this->stringField_11->insert($this->connection);
        
        $this->bioSearchFilter->insert($this->connection);
        $this->stringFieldSearchFilterOne->insert($this->connection);
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
        $this->seeStatusCode(200);
        
        $stringFieldSearchFilterOneEntry = [
            'id' => $this->stringFieldSearchFilterOne->id,
            'StringField_id' => $this->stringField_11->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('StringFieldSearchFilter', $stringFieldSearchFilterOneEntry);
    }
    public function test_stringFieldNotFoundInBioFormAggregate_404()
    {
        $this->bioFormTwo->insert($this->connection);
        $this->stringField_11->form = $this->bioFormTwo->form;
        
        $this->executeBioSearchFilter_stringField();
        $this->seeStatusCode(404);
    }
    
    protected function executeBioSearchFilter_textAreaField()
    {
        $this->bioFormOne->insert($this->connection);
        $this->textAreaField_11->insert($this->connection);
        
        $this->bioSearchFilterRequest['bioForms'][0]['textAreaFields'][] = [
            'id' => $this->textAreaField_11->id,
            'comparisonType' => 2,
        ];
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
    }
    public function test_containNonExistingTextAreaFieldFilter_addNewTextAreaFieldSearchFilter()
    {
        $this->executeBioSearchFilter_textAreaField();
        $this->seeStatusCode(200);
        
        $textAreaFieldSearchFilterOneResponse = [
            'textAreaField' => [
                'id' => $this->textAreaField_11->id,
                'name' => $this->textAreaField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($textAreaFieldSearchFilterOneResponse);
        
        $textAreaFieldSearchFilterOneEntry = [
            'TextAreaField_id' => $this->textAreaField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('TextAreaFieldSearchFilter', $textAreaFieldSearchFilterOneEntry);
    }
    public function test_containFilterCorrespondWithSameTextAreaField_update()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->textAreaFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_textAreaField();
        $this->seeStatusCode(200);
        
        $textAreaFieldSearchFilterOneResponse = [
            'textAreaField' => [
                'id' => $this->textAreaField_11->id,
                'name' => $this->textAreaField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($textAreaFieldSearchFilterOneResponse);
        
        $textAreaFieldSearchFilterOneEntry = [
            'id' => $this->textAreaFieldSearchFilterOne->id,
            'TextAreaField_id' => $this->textAreaField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('TextAreaFieldSearchFilter', $textAreaFieldSearchFilterOneEntry);
    }
    public function test_containDisabledFilterCorrespondWithUpdatedTextAreaField_enable()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->textAreaFieldSearchFilterOne->disabled = true;
        $this->textAreaFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_textAreaField();
        $this->seeStatusCode(200);
        
        $textAreaFieldSearchFilterOneResponse = [
            'textAreaField' => [
                'id' => $this->textAreaField_11->id,
                'name' => $this->textAreaField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($textAreaFieldSearchFilterOneResponse);
        
        $textAreaFieldSearchFilterOneEntry = [
            'id' => $this->textAreaFieldSearchFilterOne->id,
            'TextAreaField_id' => $this->textAreaField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('TextAreaFieldSearchFilter', $textAreaFieldSearchFilterOneEntry);
    }
    public function test_containIrrelevanFilter_NotCorrespondWithAnyTextAreaField_disabled()
    {
        $this->bioFormOne->insert($this->connection);
        $this->textAreaField_11->insert($this->connection);
        
        $this->bioSearchFilter->insert($this->connection);
        $this->textAreaFieldSearchFilterOne->insert($this->connection);
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
        $this->seeStatusCode(200);
        
        $textAreaFieldSearchFilterOneEntry = [
            'id' => $this->textAreaFieldSearchFilterOne->id,
            'TextAreaField_id' => $this->textAreaField_11->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('TextAreaFieldSearchFilter', $textAreaFieldSearchFilterOneEntry);
    }
    public function test_textAreaFieldNotFoundInBioFormAggregate_404()
    {
        $this->bioFormTwo->insert($this->connection);
        $this->textAreaField_11->form = $this->bioFormTwo->form;
        
        $this->executeBioSearchFilter_textAreaField();
        $this->seeStatusCode(404);
    }
    
    protected function executeBioSearchFilter_singleSelectField()
    {
        $this->bioFormOne->insert($this->connection);
        $this->singleSelectField_11->insert($this->connection);
        
        $this->bioSearchFilterRequest['bioForms'][0]['singleSelectFields'][] = [
            'id' => $this->singleSelectField_11->id,
            'comparisonType' => 2,
        ];
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
    }
    public function test_containNonExistingSingleSelectFieldFilter_addNewSingleSelectFieldSearchFilter()
    {
        $this->executeBioSearchFilter_singleSelectField();
        $this->seeStatusCode(200);
        
        $singleSelectFieldSearchFilterOneResponse = [
            'singleSelectField' => [
                'id' => $this->singleSelectField_11->id,
                'name' => $this->singleSelectField_11->selectField->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($singleSelectFieldSearchFilterOneResponse);
        
        $singleSelectFieldSearchFilterOneEntry = [
            'SingleSelectField_id' => $this->singleSelectField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('SingleSelectFieldSearchFilter', $singleSelectFieldSearchFilterOneEntry);
    }
    public function test_containFilterCorrespondWithSameSingleSelectField_update()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->singleSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_singleSelectField();
        $this->seeStatusCode(200);
        
        $singleSelectFieldSearchFilterOneResponse = [
            'singleSelectField' => [
                'id' => $this->singleSelectField_11->id,
                'name' => $this->singleSelectField_11->selectField->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($singleSelectFieldSearchFilterOneResponse);
        
        $singleSelectFieldSearchFilterOneEntry = [
            'id' => $this->singleSelectFieldSearchFilterOne->id,
            'SingleSelectField_id' => $this->singleSelectField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('SingleSelectFieldSearchFilter', $singleSelectFieldSearchFilterOneEntry);
    }
    public function test_containDisabledFilterCorrespondWithUpdatedSingleSelectField_enable()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->singleSelectFieldSearchFilterOne->disabled = true;
        $this->singleSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_singleSelectField();
        $this->seeStatusCode(200);
        
        $singleSelectFieldSearchFilterOneResponse = [
            'singleSelectField' => [
                'id' => $this->singleSelectField_11->id,
                'name' => $this->singleSelectField_11->selectField->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($singleSelectFieldSearchFilterOneResponse);
        
        $singleSelectFieldSearchFilterOneEntry = [
            'id' => $this->singleSelectFieldSearchFilterOne->id,
            'SingleSelectField_id' => $this->singleSelectField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('SingleSelectFieldSearchFilter', $singleSelectFieldSearchFilterOneEntry);
    }
    public function test_containIrrelevanFilter_NotCorrespondWithAnySingleSelectField_disabled()
    {
        $this->bioFormOne->insert($this->connection);
        $this->singleSelectField_11->insert($this->connection);
        
        $this->bioSearchFilter->insert($this->connection);
        $this->singleSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
        $this->seeStatusCode(200);
        
        $singleSelectFieldSearchFilterOneEntry = [
            'id' => $this->singleSelectFieldSearchFilterOne->id,
            'SingleSelectField_id' => $this->singleSelectField_11->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('SingleSelectFieldSearchFilter', $singleSelectFieldSearchFilterOneEntry);
    }
    public function test_singleSelectFieldNotFoundInBioFormAggregate_404()
    {
        $this->bioFormTwo->insert($this->connection);
        $this->singleSelectField_11->form = $this->bioFormTwo->form;
        
        $this->executeBioSearchFilter_singleSelectField();
        $this->seeStatusCode(404);
    }
    
    protected function executeBioSearchFilter_multiSelectField()
    {
        $this->bioFormOne->insert($this->connection);
        $this->multiSelectField_11->insert($this->connection);
        
        $this->bioSearchFilterRequest['bioForms'][0]['multiSelectFields'][] = [
            'id' => $this->multiSelectField_11->id,
            'comparisonType' => 2,
        ];
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
    }
    public function test_containNonExistingMultiSelectFieldFilter_addNewMultiSelectFieldSearchFilter()
    {
        $this->executeBioSearchFilter_multiSelectField();
        $this->seeStatusCode(200);
        
        $multiSelectFieldSearchFilterOneResponse = [
            'multiSelectField' => [
                'id' => $this->multiSelectField_11->id,
                'name' => $this->multiSelectField_11->selectField->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($multiSelectFieldSearchFilterOneResponse);
        
        $multiSelectFieldSearchFilterOneEntry = [
            'MultiSelectField_id' => $this->multiSelectField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('MultiSelectFieldSearchFilter', $multiSelectFieldSearchFilterOneEntry);
    }
    public function test_containFilterCorrespondWithSameMultiSelectField_update()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->multiSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_multiSelectField();
        $this->seeStatusCode(200);
        
        $multiSelectFieldSearchFilterOneResponse = [
            'multiSelectField' => [
                'id' => $this->multiSelectField_11->id,
                'name' => $this->multiSelectField_11->selectField->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($multiSelectFieldSearchFilterOneResponse);
        
        $multiSelectFieldSearchFilterOneEntry = [
            'id' => $this->multiSelectFieldSearchFilterOne->id,
            'MultiSelectField_id' => $this->multiSelectField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('MultiSelectFieldSearchFilter', $multiSelectFieldSearchFilterOneEntry);
    }
    public function test_containDisabledFilterCorrespondWithUpdatedMultiSelectField_enable()
    {
        $this->bioSearchFilter->insert($this->connection);
        $this->multiSelectFieldSearchFilterOne->disabled = true;
        $this->multiSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->executeBioSearchFilter_multiSelectField();
        $this->seeStatusCode(200);
        
        $multiSelectFieldSearchFilterOneResponse = [
            'multiSelectField' => [
                'id' => $this->multiSelectField_11->id,
                'name' => $this->multiSelectField_11->selectField->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeJsonContains($multiSelectFieldSearchFilterOneResponse);
        
        $multiSelectFieldSearchFilterOneEntry = [
            'id' => $this->multiSelectFieldSearchFilterOne->id,
            'MultiSelectField_id' => $this->multiSelectField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('MultiSelectFieldSearchFilter', $multiSelectFieldSearchFilterOneEntry);
    }
    public function test_containIrrelevanFilter_NotCorrespondWithAnyMultiSelectField_disabled()
    {
        $this->bioFormOne->insert($this->connection);
        $this->multiSelectField_11->insert($this->connection);
        
        $this->bioSearchFilter->insert($this->connection);
        $this->multiSelectFieldSearchFilterOne->insert($this->connection);
        
        $this->put($this->setBioFormFilterUri, $this->bioSearchFilterRequest, $this->manager->token);
        $this->seeStatusCode(200);
        
        $multiSelectFieldSearchFilterOneEntry = [
            'id' => $this->multiSelectFieldSearchFilterOne->id,
            'MultiSelectField_id' => $this->multiSelectField_11->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('MultiSelectFieldSearchFilter', $multiSelectFieldSearchFilterOneEntry);
    }
    public function test_multiSelectFieldNotFoundInBioFormAggregate_404()
    {
        $this->bioFormTwo->insert($this->connection);
        $this->multiSelectField_11->form = $this->bioFormTwo->form;
        
        $this->executeBioSearchFilter_multiSelectField();
        $this->seeStatusCode(404);
    }
}
