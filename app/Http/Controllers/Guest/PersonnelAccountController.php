<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Config\EventList;
use Notification\ {
    Application\Listener\PersonnelResetPasswordCodeGeneratedListener,
    Application\Service\CreatePersonnelResetPasswordMail,
    Domain\Model\Firm\Personnel as Personnel2,
    Domain\Model\Firm\Personnel\PersonnelMail
};
use Resources\Application\Event\Dispatcher;
use User\ {
    Application\Service\Personnel\GenerateResetPasswordCode,
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

    public function generateResetPasswordCode()
    {
        $service = $this->buildGenerateResetPasswordCodeService();
        
        $firmIdentifier = $this->stripTagsInputRequest("firmIdentifier");
        $email = $this->stripTagsInputRequest("email");
        
        $service->execute($firmIdentifier, $email);
        
        $response = $this->commandOkResponse();
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    protected function buildResetPasswordService()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        return new ResetPassword($personnelRepository);
    }

    protected function buildGenerateResetPasswordCodeService()
    {
        $listener = new PersonnelResetPasswordCodeGeneratedListener($this->buildCreatePersonnelResetPasswordMail());

        $personnelRepository = $this->em->getRepository(Personnel::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(EventList::PERSONNEL_RESET_PASSWORD_CODE_GENERATED, $listener);

        return new GenerateResetPasswordCode($personnelRepository, $dispatcher);
    }

    protected function buildCreatePersonnelResetPasswordMail()
    {
        $personnelMailRepository = $this->em->getRepository(PersonnelMail::class);
        $personnelRepository = $this->em->getRepository(Personnel2::class);

        return new CreatePersonnelResetPasswordMail($personnelMailRepository, $personnelRepository);
    }

}
