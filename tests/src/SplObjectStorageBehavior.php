<?php

use Tests\TestBase;

class SplObjectStorageBehaviorTest extends TestBase
{
    protected $storage;
    protected $objectOne;
    protected $objectTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = new SplObjectStorage();
        $this->objectOne = new stdClass;
        $this->objectTwo = new stdClass;
    }
    public function test_attach()
    {
        $this->storage->attach($this->objectOne);
        $this->assertTrue($this->storage->contains($this->objectOne));
    }
    public function test_attachWithOtherObjectAsData()
    {
        $this->storage->attach($this->objectOne, $this->objectTwo);
        $this->assertTrue($this->storage->contains($this->objectOne));
        $this->assertEquals($this->objectTwo, $this->storage[$this->objectOne]);
    }
    public function test_detach()
    {
        $this->storage->attach($this->objectOne, $this->objectTwo);
        $this->assertEquals(1, $this->storage->count());
        $this->assertTrue($this->storage->contains($this->objectOne));
        $this->storage->detach($this->objectOne);
        $this->assertEmpty($this->storage->count());
    }
    
    public function test_json()
    {
        $data = [
            "assignmentFieldValues" => [
                [
                    "assignmentFieldId" => "id",
                    "value" => 123.123,
                ],
                [
                    "assignmentFieldId" => "otherId",
                    "value" => 987.987,
                ],
            ],
        ];
print_r(json_encode($data));
var_dump(base64_decode("barapraja_mcp_2020"));
var_dump(base64_decode("barapraja_mcp_2020"));
    }
}
