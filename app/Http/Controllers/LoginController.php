<?php

namespace App\Http\Controllers;

use Bara\ {
    Application\Service\AdminLogin,
    Domain\Model\Admin
};
use Firebase\JWT\JWT;
use Firm\ {
    Application\Service\Firm\ManagerLogin,
    Domain\Model\Firm\Manager
};
use function env;
use function response;

class LoginController extends Controller
{
    public function adminLogin()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        $service = new AdminLogin($adminRepository);
        
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        $admin = $service->execute($email, $password);
        
        $data = [
            "id" => $admin->getId(),
            "name" => $admin->getName(),
        ];
        $identifier = [
            "adminId" => $admin->getId(),
        ];
        $token = $this->generateJwtToken($identifier);
        return $this->buildCredentialsResponse($data, $token);
    }
    
    public function managerLogin()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $service = new ManagerLogin($managerRepository);
        
        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        $manager = $service->execute($firmIdentifier, $email, $password);
        
        $data = [
            "id" => $manager->getId(),
            "name" => $manager->getName(),
        ];
        $identifier = [
            "firmId" => $manager->getFirm()->getId(),
            "managerId" => $manager->getId(),
        ];
        $token = $this->generateJwtToken($identifier);
        return $this->buildCredentialsResponse($data, $token);
    }
    
    private function generateJwtToken(array $identifier)
    {
        $payload = [
            'iss' => env('JWT_SERVER'),
            'jti' => env('JWT_TOKEN_ID'),
            'iat' => time(),
            'exp' => time() + env('JWT_TIMEOUT'),
            'nbf' => time() + env('JWT_ACTIVE_AFTER'),
            'data' => $identifier
        ];
        $key = base64_decode(env('JWT_KEY'));
        return JWT::encode($payload, $key);
    }

    private function buildCredentialsResponse($data, $token)
    {
        $content = [
            "data" => $data,
            "meta" => [
                "code" => 200,
                "type" => 'OK'
            ],
            "credentials" => [
                "token" => $token,
                "valid_until" => time() + env('JWT_TIMEOUT')
            ]
        ];
        return response()->json($content);
    }
    
}
