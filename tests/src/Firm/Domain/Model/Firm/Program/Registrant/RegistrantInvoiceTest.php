<?php

namespace Firm\Domain\Model\Firm\Program\Registrant;

use Tests\TestBase;

class RegistrantInvoiceTest extends TestBase
{
    protected $registrant;
    protected $invoice;
    protected $id = 'registrant-invoice-id';
    protected $registrantInvoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrant = $this->buildMockOfClass(\Firm\Domain\Model\Firm\Program\Registrant::class);
        $this->invoice = $this->buildMockOfClass(\SharedContext\Domain\Model\Invoice::class);
        $this->registrantInvoice = new TestableRegistrantInvoice($this->registrant, 'id', $this->invoice);
    }
    
    protected function construct()
    {
        return new TestableRegistrantInvoice($this->registrant, $this->id, $this->invoice);
    }
    public function test_construct_setProperties()
    {
        $registrantInvoice = $this->construct();
        $this->assertSame($this->registrant, $registrantInvoice->registrant);
        $this->assertSame($this->id, $registrantInvoice->id);
        $this->assertSame($this->invoice, $registrantInvoice->invoice);
    }
    
    //
    protected function settle()
    {
        $this->registrantInvoice->settle();
    }
    public function test_settle_settleInvoice()
    {
        $this->invoice->expects($this->once())
                ->method('settle');
        $this->settle();
    }
    
}

class TestableRegistrantInvoice extends RegistrantInvoice
{
    public $registrant;
    public $id;
    public $invoice;
}
