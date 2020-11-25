<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use User\ {
    Application\Service\Personnel\ResetPassword,
    Domain\Model\Personnel
};

class PersonnelAccountController extends Controller
{
    public function resetPassword()
    {
        $service = $this->buildResetPasswordService();
        $firmIdentifier = $this->stripTagsInputRequest("firmIdentifier");
        $email = $this->stripTagsInputRequest("email");
        $resetPasswordCode = $this->stripTagsInputRequest("resetPasswordCode");
        $password = $this->stripTagsInputRequest("password");
        
        $service->execute($firmIdentifier, $email, $resetPasswordCode, $password);
        
        return $this->commandOkResponse();
    }
    
    protected function buildResetPasswordService()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        return new ResetPassword($personnelRepository);
    }
}
