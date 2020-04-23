<?php

namespace Tests\Controllers\RecordPreparation;

use Resources\Domain\ValueObject\Password;

class TestablePassword extends Password
{
    public function getHashedPassword()
    {
        return $this->password;
    }
}
