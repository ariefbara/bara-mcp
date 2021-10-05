<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;

class SponsorTest extends TestBase
{
    protected $program;
    protected $sponsor;
    protected $id = 'new-id', $name = 'new sponsor name', $logo, $website = "website.sponsor.id";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $sponsorData = new SponsorData('name', null, null);
        $this->sponsor = new TestableSponsor($this->program, 'id', $sponsorData);
        
        $this->logo = $this->buildMockOfClass(FirmFileInfo::class);
    }
    protected function buildSponsorData()
    {
        return new SponsorData($this->name, $this->logo, $this->website);
    }
    
    protected function construct()
    {
        return new TestableSponsor($this->program, $this->id, $this->buildSponsorData());
    }
    public function test_construct_setProperties()
    {
        $sponsor = $this->construct();
        $this->assertEquals($this->program, $sponsor->program);
        $this->assertEquals($this->id, $sponsor->id);
        $this->assertFalse($sponsor->disabled);
        $this->assertEquals($this->name, $sponsor->name);
        $this->assertEquals($this->website, $sponsor->website);
        $this->assertEquals($this->logo, $sponsor->logo);
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = "";
        $this->assertRegularExceptionThrowed(function (){
            $this->construct();
        }, "Bad Request", "bad request: name is mandatory");
    }
    public function test_construct_invalidWebsiteFormat_badRequest()
    {
        $this->website = "bad request";
        $this->assertRegularExceptionThrowed(function (){
            $this->construct();
        }, "Bad Request", "bad request: website must be a valid domain format");
    }
    public function test_construct_assertLogoUsableInProgram()
    {
        $this->program->expects($this->once())
                ->method('assertFileUsable')
                ->with($this->logo);
        $this->construct();
    }
    
    protected function update()
    {
        $this->sponsor->update($this->buildSponsorData());
    }
    public function test_update_setProperties()
    {
        $this->update();
        $this->assertEquals($this->name, $this->sponsor->name);
        $this->assertEquals($this->website, $this->sponsor->website);
        $this->assertEquals($this->logo, $this->sponsor->logo);
    }
    public function test_update_emptyName_badRequest()
    {
        $this->name = "";
        $this->assertRegularExceptionThrowed(function (){
            $this->update();
        }, "Bad Request", "bad request: name is mandatory");
    }
    public function test_update_invalidWebsiteFormat_badRequest()
    {
        $this->website = "bad request";
        $this->assertRegularExceptionThrowed(function (){
            $this->update();
        }, "Bad Request", "bad request: website must be a valid domain format");
    }
    public function test_update_assertLogoUsableInProgram()
    {
        $this->program->expects($this->once())
                ->method('assertFileUsable')
                ->with($this->logo);
        $this->update();
    }
    
    public function test_disable_setDisabled()
    {
        $this->sponsor->disable();
        $this->assertTrue($this->sponsor->disabled);
    }
    
    public function test_enable_setEnabled()
    {
        $this->sponsor->disabled = true;
        $this->sponsor->enable();
        $this->assertFalse($this->sponsor->disabled);
    }
    
    protected function assertManageableInProgram()
    {
        $this->sponsor->assertManageableInProgram($this->program);
    }
    public function test_assertManageableInProgram_sameProgram()
    {
        $this->assertManageableInProgram();
        $this->markAsSuccess();
    }
    public function test_assertManageableInProgram_differentProgram_forbidden()
    {
        $this->sponsor->program = $this->buildMockOfClass(Program::class);
        $this->assertRegularExceptionThrowed(function (){
            $this->assertManageableInProgram();
        }, "Forbidden", "forbidden: unable to manage sponsor, probably belongs to other program");
    }
}

class TestableSponsor extends Sponsor
{
    public $program;
    public $id;
    public $disabled;
    public $name;
    public $website;
    public $logo;
}
