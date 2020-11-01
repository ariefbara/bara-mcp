<?php

namespace Participant\Domain\DependencyModel\User;

use Tests\TestBase;

class UserFileInfoTest extends TestBase
{
    protected $userFileInfo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userFileInfo = new TestableUserFileInfo();
    }
    
    public function test_belongsToUser_sameUserId_returnTrue()
    {
        $this->assertTrue($this->userFileInfo->belongsToUser($this->userFileInfo->userId));
    }
    public function test_belongsToUser_differentUserId_returnFalse()
    {
        $this->assertFalse($this->userFileInfo->belongsToUser("differentId"));
    }
}

class TestableUserFileInfo extends UserFileInfo
{
    public $userId = "userId";
    public $id;
    public $fileInfo;
    
    function __construct()
    {
        parent::__construct();
    }
}
