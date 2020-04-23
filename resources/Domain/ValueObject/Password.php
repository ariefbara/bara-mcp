<?php
namespace Resources\Domain\ValueObject;

use Resources\ValidationService;
use Resources\ValidationRule;

class Password
{

    protected $password;

    function __construct($password)
    {
//         $regex = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/";//no whitespace
        $regex = "/^(?=.*[A-Za-z ])(?=.*\d)[A-Za-z \d]{8,}$/";//whitespace allowed
        $errorDetail = "bad request: password required at least 8 character long contain alphabet and number";
        (new ValidationService())
            ->addRule(ValidationRule::regex($regex))
            ->execute($password, $errorDetail);

        $options = [
            'cost' => 10
        ];
        $this->password = password_hash($password, PASSWORD_DEFAULT, $options);
    }
    
    public function match(string $password): bool {
        return password_verify($password, $this->password);
    }
}

