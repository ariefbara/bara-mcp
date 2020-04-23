<?php

namespace Tests\Controllers\RecordPreparation;

class RecordOfAdmin implements Record
{
    public $id, $name, $email, $password, $removed = false;
    public $rawPassword;
    public $token;
    
    public function __construct($index, $email, $rawPassword)
    {
        $this->id = "admin-$index-id";
        $this->name = "admin $index name";
        $this->email = $email;
        $this->rawPassword = $rawPassword;
        $this->password = (new TestablePassword($rawPassword))->getHashedPassword();
        $data = [
            "adminId" => $this->id,
        ];
        $this->token = JwtHeaderTokenGenerator::generate($data);
    }


    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
            "removed" => $this->removed,
        ];
    }

}
