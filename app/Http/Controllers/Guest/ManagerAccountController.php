<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Notification\ {
    Application\Listener\ManagerResetPasswordCodeGeneratedListener,
    Application\Service\CreateManagerResetPasswordMail,
    Domain\Model\Firm\Manager as Manager2,
    Domain\Model\Firm\Manager\ManagerMail
};
use Resources\Application\Event\Dispatcher;
use User\ {
    Application\Service\Manager\GenerateResetPasswordCode,
    Application\Service\Manager\ResetPassword,
    Domain\Model\Manager
};

class ManagerAccountController extends Controller
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
    
    public function generateResetPasswordCode()
    {
        $service = $this->buildGenerateResetPasswordCodeService();
        $firmIdentifier = $this->stripTagsInputRequest("firmIdentifier");
        $email = $this->stripTagsInputRequest("email");
        
        $service->execute($firmIdentifier, $email);
        return $this->commandOkResponse();
    }
    
    protected function buildResetPasswordService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        return new ResetPassword($managerRepository);
    }
    
    protected function buildGenerateResetPasswordCodeService()
    {
        $createManagerResetPasswordMail = $this->buildCreateManagerResetPasswordMail();
        $sendImmediateMail = $this->buildSendImmediateMail();
        $listener = new ManagerResetPasswordCodeGeneratedListener($createManagerResetPasswordMail, $sendImmediateMail);
        
        $managerRepository = $this->em->getRepository(Manager::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(\Config\EventList::MANAGER_RESET_PASSWORD_CODE_GENERATED, $listener);
        
        return new GenerateResetPasswordCode($managerRepository, $dispatcher);
    }
    protected function buildCreateManagerResetPasswordMail()
    {
        $managerMailRepository = $this->em->getRepository(ManagerMail::class);
        $managerRepository = $this->em->getRepository(Manager2::class);
        
        return new CreateManagerResetPasswordMail($managerMailRepository, $managerRepository);
    }
}
