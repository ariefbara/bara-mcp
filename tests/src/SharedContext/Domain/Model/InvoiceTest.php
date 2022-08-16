<?php

namespace SharedContext\Domain\Model;

use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class InvoiceTest extends TestBase
{
    protected $id = 'invoice-id', $expiredTime, $paymentLink = 'random string represent payment link';
    protected $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->expiredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy('+7 days');
        $this->invoice = new TestableInvoice('id', $this->expiredTime, $this->paymentLink);
    }
    
    //
    protected function construct()
    {
        return new TestableInvoice($this->id, $this->expiredTime, $this->paymentLink);
    }
    public function test_connstruct_setProperties()
    {
        $invoice = $this->construct();
        $this->assertSame($this->id, $invoice->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $invoice->issuedTime);
        $this->assertSame($this->expiredTime, $invoice->expiredTime);
        $this->assertSame($this->paymentLink, $invoice->paymentLink);
        $this->assertFalse($invoice->settled);
    }
    
    //
    protected function settle()
    {
        $this->invoice->settle();  
    }
    public function test_settle_setSettle()
    {
        $this->settle();
        $this->assertTrue($this->invoice->settled);
    }
    public function test_settle_alreadySettled_forbidden()
    {
        $this->invoice->settled = true;
        $this->assertRegularExceptionThrowed(function(){
            $this->settle();
        }, 'Forbidden', 'invoice already settled');
    }
}

class TestableInvoice extends Invoice
{
    public $id;
    public $issuedTime;
    public $expiredTime;
    public $paymentLink;
    public $settled;
}
