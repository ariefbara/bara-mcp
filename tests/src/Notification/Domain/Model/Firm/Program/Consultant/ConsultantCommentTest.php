<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\Model\Firm\Program\Consultant;
use Tests\TestBase;

class ConsultantCommentTest extends TestBase
{
    protected $consultantComment;
    protected $consultant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantComment = new TestableConsultantComment();
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantComment->consultant = $this->consultant;
    }
    
    public function test_getConsultantMailRecipient_returnConsultantsGetPersonnelMailRecipientResult()
    {
        $this->consultant->expects($this->once())
                ->method('getPersonnelMailRecipient');
        $this->consultantComment->getConsultantMailRecipient();
    }
}

class TestableConsultantComment extends ConsultantComment
{
    public $id;
    public $consultant;
    public $comment;
    
    function __construct()
    {
        parent::__construct();
    }
}
