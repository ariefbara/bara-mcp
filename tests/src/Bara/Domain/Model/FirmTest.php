<?php

namespace Bara\Domain\Model;

use Bara\Domain\Model\Firm\{
    Manager,
    ManagerData
};
use Query\Domain\Model\FirmWhitelableInfo;
use Tests\TestBase;

class FirmTest extends TestBase
{

    protected $firm;
    protected $admin;
    protected $id = 'new-id', $name = 'new firm name', $identifier = 'new_firm_identifier',
            $whitelableUrl = 'http://barapraja.com/konsulta', $whitelableMailSenderAddress = 'noreply@barapraja.com',
            $whitelableWhitelableSenderName = 'team konsulta barapraja';
    protected $managerData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->managerData = $this->buildMockOfClass(ManagerData::class);
        $this->managerData->expects($this->any())
                ->method('getName')
                ->willReturn('manager name');
        $this->managerData->expects($this->any())
                ->method('getEmail')
                ->willReturn('manager@email.org');
        $this->managerData->expects($this->any())
                ->method('getPassword')
                ->willReturn('password123');

        $firmData = new FirmData('name', 'identifier', 'http://firm.com', 'admin@firm.com', 'firm name');
        $this->firm = new TestableFirm('id', $firmData, $this->managerData);
    }

    protected function getFirmData()
    {
        return new FirmData(
                $this->name, $this->identifier, $this->whitelableUrl, $this->whitelableMailSenderAddress,
                $this->whitelableWhitelableSenderName);
    }

    private function executeConstruct()
    {
        return new TestableFirm($this->id, $this->getFirmData(), $this->managerData);
    }

    public function test_construct_setProperties()
    {
        $firm = $this->executeConstruct();
        $this->assertEquals($this->id, $firm->id);
        $this->assertEquals($this->name, $firm->name);
        $this->assertEquals($this->identifier, $firm->identifier);
        $this->assertFalse($firm->suspended);
        $firmWhitelableInfo = new FirmWhitelableInfo($this->whitelableUrl, $this->whitelableMailSenderAddress,
                $this->whitelableWhitelableSenderName);
        $this->assertEquals($firmWhitelableInfo, $firm->firmWhitelableInfo);
    }

    public function test_construct_setManager()
    {
        $firm = $this->executeConstruct();
        $this->assertInstanceOf(Manager::class, $firm->managers->first());
    }

    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: firm name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_identifierContainNonAlphanumericUnderscoreOrHypen_throwEx()
    {
        $this->identifier = 'containINvalidChar*#$';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: firm identifier is required and must only contain alphanumeric, underscore and hypen character without whitespace';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_identifierContainWhitespace_throwEx()
    {
        $this->identifier = 'contain whitespace';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: firm identifier is required and must only contain alphanumeric, underscore and hypen character without whitespace';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_suspend_setSuspendedTrue()
    {
        $this->firm->suspend();
        $this->assertTrue($this->firm->suspended);
    }

}

class TestableFirm extends Firm
{

    public $id, $name, $identifier, $suspended, $firmWhitelableInfo;
    public $managers;

}
