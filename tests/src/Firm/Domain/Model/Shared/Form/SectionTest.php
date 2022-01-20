<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Tests\TestBase;

class SectionTest extends TestBase
{
    protected $form;
    protected $id = 'newId', $name = 'new section name', $position = 'new section position';
    protected $section;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->buildMockOfClass(Form::class);
        $sectionData = new SectionData('section name', 'section position');
        $this->section = new TestableSection($this->form, 'id', $sectionData);
    }
    
    protected function getSectionData()
    {
        return new SectionData($this->name, $this->position);
    }
    
    protected function construct()
    {
        return new TestableSection($this->form, $this->id, $this->getSectionData());
    }
    public function test_construct_setProperties()
    {
        $section = $this->construct();
        $this->assertSame($this->form, $section->form);
        $this->assertSame($this->id, $section->id);
        $this->assertSame($this->name, $section->name);
        $this->assertSame($this->position, $section->position);
        $this->assertFalse($section->removed);
    }
    
    protected function update()
    {
        $this->section->update($this->getSectionData());
    }
    public function test_update_updateProperties()
    {
        $this->update();
        $this->assertSame($this->name, $this->section->name);
        $this->assertSame($this->position, $this->section->position);
    }
    
    protected function remove()
    {
        $this->section->remove();
    }
    public function test_remove_scenario_expectedResult()
    {
        $this->remove();
        $this->assertTrue($this->section->removed);
    }
}

class TestableSection extends Section
{
    public $form;
    public $id;
    public $name;
    public $position;
    public $removed;
}
