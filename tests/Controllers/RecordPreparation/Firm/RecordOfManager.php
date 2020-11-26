<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    JwtHeaderTokenGenerator,
    Record,
    RecordOfFirm,
    TestablePassword
};

class RecordOfManager implements Record
{
    /**
     *
     * @var RecordOfFirm
     */
    public $firm;
    public $id, $name, $email, $password, $phone = "", $joinTime, $removed = false;
    public $resetPasswordCode;
    public $resetPasswordCodeExpiredTime;
    public $rawPassword;
    public $token;
    
    public function __construct(RecordOfFirm $firm, $index, $email, $rawPassword)
    {
        $this->firm = $firm;
        $this->id = "manager-$index-id";
        $this->name = "manager $index name";
        $this->email = $email;
        $this->password = (new TestablePassword($rawPassword))->getHashedPassword();
        $this->phone = "";
        $this->joinTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->resetPasswordCode = "string-represent-reset-token";
        $this->resetPasswordCodeExpiredTime = (new \DateTimeImmutable("+8 hours"))->format("Y-m-d H:i:s");
        $this->removed = false;
        
        $this->rawPassword = $rawPassword;
        $data = [
            "firmId" => $this->firm->id,
            "managerId" => $this->id,
        ];
        $this->token = JwtHeaderTokenGenerator::generate($data);
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
            "phone" => $this->phone,
            "joinTime" => $this->joinTime,
            "resetPasswordCode" => $this->resetPasswordCode,
            "resetPasswordCodeExpiredTime" => $this->resetPasswordCodeExpiredTime,
            "removed" => $this->removed,
        ];
    }

}
