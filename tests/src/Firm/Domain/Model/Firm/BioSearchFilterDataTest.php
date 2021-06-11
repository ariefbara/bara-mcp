<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm\BioSearchFilter\IntegerFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilter\MultiSelectFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilter\SingleSelectFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilter\StringFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilter\TextAreaFieldSearchFilterData;
use Firm\Domain\Model\Shared\Form\IntegerField;
use Firm\Domain\Model\Shared\Form\MultiSelectField;
use Firm\Domain\Model\Shared\Form\SingleSelectField;
use Firm\Domain\Model\Shared\Form\StringField;
use Firm\Domain\Model\Shared\Form\TextAreaField;
use SplObjectStorage;
use Tests\TestBase;

class BioSearchFilterDataTest extends TestBase
{
    protected $bioSearchFilterData;
    protected $storage;
    protected $comparisonType = 1;
    protected $integerField;
    protected $stringField;
    protected $textAreaField;
    protected $singleSelectField;
    protected $multiSelectField;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->bioSearchFilterData = new TestableBioSearchFilterData();
        $this->storage = $this->buildMockOfClass(SplObjectStorage::class);
        
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->stringField = $this->buildMockOfClass(StringField::class);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
    }
    
    public function test_splObjectStorageSpike()
    {
        $storage = new SplObjectStorage();
        $storage->attach($this->integerField, $this->comparisonType);
        $this->assertTrue($storage->contains($this->integerField));
        $this->assertSame($this->comparisonType, $storage[$this->integerField]);
        foreach ($storage as $key => $value) {
            $this->assertSame($this->integerField, $value);
            $this->assertSame($this->comparisonType, $storage[$value]);
        }
        $storage->detach($this->integerField);
        $this->assertEmpty($storage->count());
    }
    
    public function test_construct_setProperties()
    {
        $bioSearchFilterData = new TestableBioSearchFilterData();
        $this->assertInstanceOf(SplObjectStorage::class, $bioSearchFilterData->integerFieldStorage);
        $this->assertInstanceOf(SplObjectStorage::class, $bioSearchFilterData->stringFieldStorage);
        $this->assertInstanceOf(SplObjectStorage::class, $bioSearchFilterData->textAreaFieldStorage);
        $this->assertInstanceOf(SplObjectStorage::class, $bioSearchFilterData->singleSelectFieldStorage);
        $this->assertInstanceOf(SplObjectStorage::class, $bioSearchFilterData->multiSelectFieldStorage);
    }
    
    public function test_addIntegerFieldFilter_addIntegerFieldFilterToCollection()
    {
        $this->bioSearchFilterData->integerFieldStorage = $this->storage;
        
        $this->storage->expects($this->once())
                ->method('attach')
                ->with($this->integerField, $this->comparisonType);
        $this->bioSearchFilterData->addIntegerFieldFilter($this->integerField, $this->comparisonType);
    }
    public function test_pullComparisonTypeCorresondWithIntegerField_returnComparisonType()
    {
        $this->bioSearchFilterData->integerFieldStorage->attach($this->integerField, $this->comparisonType);
        $this->assertEquals($this->comparisonType, $this->bioSearchFilterData->pullComparisonTypeCorrespondWithIntegerField($this->integerField));
    }
    public function test_pullComparisonTypeCorresponWithIntegerField_noIntegerFieldInStorage_returnNull()
    {
        $this->assertNull($this->bioSearchFilterData->pullComparisonTypeCorrespondWithIntegerField($this->integerField));
    }
    public function test_pullComparisonTypeCorrespondWithIntegerField_detachIntegerFieldFromStorage()
    {
        $this->bioSearchFilterData->integerFieldStorage->attach($this->integerField, $this->comparisonType);
        $this->bioSearchFilterData->pullComparisonTypeCorrespondWithIntegerField($this->integerField);
        $this->assertFalse($this->bioSearchFilterData->integerFieldStorage->contains($this->integerField));
    }
    public function test_iterateIntegerFieldSearchData_returnArrayOfIntegerFieldSearchData()
    {
        $this->bioSearchFilterData->integerFieldStorage->attach($this->integerField, $this->comparisonType);
        $this->assertEquals([new IntegerFieldSearchFilterData($this->integerField, $this->comparisonType)], $this->bioSearchFilterData->getIntegerFieldsSearchFilterDataIterator());
    }
    
    public function test_addStringFieldFilter_addStringFieldFilterToCollection()
    {
        $this->bioSearchFilterData->stringFieldStorage = $this->storage;
        
        $this->storage->expects($this->once())
                ->method('attach')
                ->with($this->stringField, $this->comparisonType);
        $this->bioSearchFilterData->addStringFieldFilter($this->stringField, $this->comparisonType);
    }
    public function test_pullComparisonTypeCorresondWithStringField_returnComparisonType()
    {
        $this->bioSearchFilterData->stringFieldStorage->attach($this->stringField, $this->comparisonType);
        $this->assertEquals($this->comparisonType, $this->bioSearchFilterData->pullComparisonTypeCorrespondWithStringField($this->stringField));
    }
    public function test_pullComparisonTypeCorresponWithStringField_noStringFieldInStorage_returnNull()
    {
        $this->assertNull($this->bioSearchFilterData->pullComparisonTypeCorrespondWithStringField($this->stringField));
    }
    public function test_pullComparisonTypeCorrespondWithStringField_detachStringFieldFromStorage()
    {
        $this->bioSearchFilterData->stringFieldStorage->attach($this->stringField, $this->comparisonType);
        $this->bioSearchFilterData->pullComparisonTypeCorrespondWithStringField($this->stringField);
        $this->assertFalse($this->bioSearchFilterData->stringFieldStorage->contains($this->stringField));
    }
    public function test_iterateStringFieldSearchData_returnArrayOfStringFieldSearchData()
    {
        $this->bioSearchFilterData->stringFieldStorage->attach($this->stringField, $this->comparisonType);
        $this->assertEquals([new StringFieldSearchFilterData($this->stringField, $this->comparisonType)], $this->bioSearchFilterData->getStringFieldsSearchFilterDataIterator());
    }
    
    public function test_addTextAreaFieldFilter_addTextAreaFieldFilterToCollection()
    {
        $this->bioSearchFilterData->textAreaFieldStorage = $this->storage;
        
        $this->storage->expects($this->once())
                ->method('attach')
                ->with($this->textAreaField, $this->comparisonType);
        $this->bioSearchFilterData->addTextAreaFieldFilter($this->textAreaField, $this->comparisonType);
    }
    public function test_pullComparisonTypeCorresondWithTextAreaField_returnComparisonType()
    {
        $this->bioSearchFilterData->textAreaFieldStorage->attach($this->textAreaField, $this->comparisonType);
        $this->assertEquals($this->comparisonType, $this->bioSearchFilterData->pullComparisonTypeCorrespondWithTextAreaField($this->textAreaField));
    }
    public function test_pullComparisonTypeCorresponWithTextAreaField_noTextAreaFieldInStorage_returnNull()
    {
        $this->assertNull($this->bioSearchFilterData->pullComparisonTypeCorrespondWithTextAreaField($this->textAreaField));
    }
    public function test_pullComparisonTypeCorrespondWithTextAreaField_detachTextAreaFieldFromStorage()
    {
        $this->bioSearchFilterData->textAreaFieldStorage->attach($this->textAreaField, $this->comparisonType);
        $this->bioSearchFilterData->pullComparisonTypeCorrespondWithTextAreaField($this->textAreaField);
        $this->assertFalse($this->bioSearchFilterData->textAreaFieldStorage->contains($this->textAreaField));
    }
    public function test_iterateTextAreaFieldSearchData_returnArrayOfTextAreaFieldSearchData()
    {
        $this->bioSearchFilterData->textAreaFieldStorage->attach($this->textAreaField, $this->comparisonType);
        $this->assertEquals([new TextAreaFieldSearchFilterData($this->textAreaField, $this->comparisonType)], $this->bioSearchFilterData->getTextAreaFieldsSearchFilterDataIterator());
    }
    
    public function test_addSingleSelectFieldFilter_addSingleSelectFieldFilterToCollection()
    {
        $this->bioSearchFilterData->singleSelectFieldStorage = $this->storage;
        
        $this->storage->expects($this->once())
                ->method('attach')
                ->with($this->singleSelectField, $this->comparisonType);
        $this->bioSearchFilterData->addSingleSelectFieldFilter($this->singleSelectField, $this->comparisonType);
    }
    public function test_pullComparisonTypeCorresondWithSingleSelectField_returnComparisonType()
    {
        $this->bioSearchFilterData->singleSelectFieldStorage->attach($this->singleSelectField, $this->comparisonType);
        $this->assertEquals($this->comparisonType, $this->bioSearchFilterData->pullComparisonTypeCorrespondWithSingleSelectField($this->singleSelectField));
    }
    public function test_pullComparisonTypeCorresponWithSingleSelectField_noSingleSelectFieldInStorage_returnNull()
    {
        $this->assertNull($this->bioSearchFilterData->pullComparisonTypeCorrespondWithSingleSelectField($this->singleSelectField));
    }
    public function test_pullComparisonTypeCorrespondWithSingleSelectField_detachSingleSelectFieldFromStorage()
    {
        $this->bioSearchFilterData->singleSelectFieldStorage->attach($this->singleSelectField, $this->comparisonType);
        $this->bioSearchFilterData->pullComparisonTypeCorrespondWithSingleSelectField($this->singleSelectField);
        $this->assertFalse($this->bioSearchFilterData->singleSelectFieldStorage->contains($this->singleSelectField));
    }
    public function test_iterateSingleSelectFieldSearchData_returnArrayOfSingleSelectFieldSearchData()
    {
        $this->bioSearchFilterData->singleSelectFieldStorage->attach($this->singleSelectField, $this->comparisonType);
        $this->assertEquals([new SingleSelectFieldSearchFilterData($this->singleSelectField, $this->comparisonType)], $this->bioSearchFilterData->getSingleSelectFieldsSearchFilterDataIterator());
    }
    
    public function test_addMultiSelectFieldFilter_addMultiSelectFieldFilterToCollection()
    {
        $this->bioSearchFilterData->multiSelectFieldStorage = $this->storage;
        
        $this->storage->expects($this->once())
                ->method('attach')
                ->with($this->multiSelectField, $this->comparisonType);
        $this->bioSearchFilterData->addMultiSelectFieldFilter($this->multiSelectField, $this->comparisonType);
    }
    public function test_pullComparisonTypeCorresondWithMultiSelectField_returnComparisonType()
    {
        $this->bioSearchFilterData->multiSelectFieldStorage->attach($this->multiSelectField, $this->comparisonType);
        $this->assertEquals($this->comparisonType, $this->bioSearchFilterData->pullComparisonTypeCorrespondWithMultiSelectField($this->multiSelectField));
    }
    public function test_pullComparisonTypeCorresponWithMultiSelectField_noMultiSelectFieldInStorage_returnNull()
    {
        $this->assertNull($this->bioSearchFilterData->pullComparisonTypeCorrespondWithMultiSelectField($this->multiSelectField));
    }
    public function test_pullComparisonTypeCorrespondWithMultiSelectField_detachMultiSelectFieldFromStorage()
    {
        $this->bioSearchFilterData->multiSelectFieldStorage->attach($this->multiSelectField, $this->comparisonType);
        $this->bioSearchFilterData->pullComparisonTypeCorrespondWithMultiSelectField($this->multiSelectField);
        $this->assertFalse($this->bioSearchFilterData->multiSelectFieldStorage->contains($this->multiSelectField));
    }
    public function test_iterateMultiSelectFieldSearchData_returnArrayOfMultiSelectFieldSearchData()
    {
        $this->bioSearchFilterData->multiSelectFieldStorage->attach($this->multiSelectField, $this->comparisonType);
        $this->assertEquals([new MultiSelectFieldSearchFilterData($this->multiSelectField, $this->comparisonType)], $this->bioSearchFilterData->getMultiSelectFieldsSearchFilterDataIterator());
    }
}

class TestableBioSearchFilterData extends BioSearchFilterData
{
    public $integerFieldStorage;
    public $stringFieldStorage;
    public $textAreaFieldStorage;
    public $singleSelectFieldStorage;
    public $multiSelectFieldStorage;
}
