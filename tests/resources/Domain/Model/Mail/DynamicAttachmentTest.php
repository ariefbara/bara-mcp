<?php
namespace Resources\Domain\Model\Mail;

use Tests\TestBase;

class DynamicAttachmentTest extends TestBase
{
    protected $fileName = 'image.pdf', $content = 'jpg file content', $contentType = 'image/jpeg';
    
    protected function setUp(): void {
        parent::setUp();
    }
    
    function test_construct_setProperties() {
        $dynamicAttachment = new TestableDynamicAttachment($this->fileName, $this->content, $this->contentType);
        $this->assertEquals($this->fileName, $dynamicAttachment->fileName);
        $this->assertEquals($this->content, $dynamicAttachment->content);
        $this->assertEquals($this->contentType, $dynamicAttachment->contentType);
    }
}
class TestableDynamicAttachment extends DynamicAttachment{
    public $fileName, $content, $contentType;
}

