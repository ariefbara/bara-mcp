<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Query\ {
    Application\Service\AdminLogin,
    Application\Service\ClientLogin,
    Application\Service\Firm\ManagerLogin,
    Application\Service\Firm\PersonnelLogin,
    Domain\Model\Admin,
    Domain\Model\Client,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\Personnel
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
    
    public function personnelLogin()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $service = new PersonnelLogin($personnelRepository);
        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        $personnel = $service->execute($firmIdentifier, $email, $password);
        
        $identifier = [
            "firmId" => $personnel->getFirm()->getId(),
            "personnelId" => $personnel->getId(),
        ];
        $token = $this->generateJwtToken($identifier);
        return $this->buildCredentialsResponse($this->arrayDataOfPersonnel($personnel), $token);
    }
    
    public function clientLogin()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ClientLogin($clientRepository);
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        
        $client = $service->execute($email, $password);
        $data = [
            "id" => $client->getId(),
            "name" => $client->getName(),
        ];
        $identifier = [
            "clientId" => $client->getId(),
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
    
    private function arrayDataOfPersonnel(Personnel $personnel): array
    {
        $data = [
            "id" => $personnel->getId(),
            "name" => $personnel->getName(),
            "programConsultants" => [],
            "programCoordinators" => [],
        ];
        foreach ($personnel->getUnremovedProgramConsultants() as $consultant) {
            $data['programConsultants'][] = [
                "id" => $consultant->getId(),
                "program" => [
                    "id" => $consultant->getProgram()->getId(),
                    "name" => $consultant->getProgram()->getName(),
                ],
            ];
        }
        foreach ($personnel->getUnremovedProgramCoordinators() as $coordinator) {
            $data['programCoordinators'][] = [
                "id" => $coordinator->getId(),
                "program" => [
                    "id" => $coordinator->getProgram()->getId(),
                    "name" => $coordinator->getProgram()->getName(),
                ],
            ];
        }
        return $data;
    }
    
}
