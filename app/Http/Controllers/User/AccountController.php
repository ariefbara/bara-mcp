<?php

namespace App\Http\Controllers\User;

use Query\ {
    Application\Service\ViewUser,
    Domain\Model\User
};
use User\ {
    Application\Service\ChangeUserPassword,
    Application\Service\ChangeUserProfile,
    Domain\Model\User as User2
};

class AccountController extends UserBaseController
{
    public function changeProfile()
    {
        $service = $this->buildChangeProfileService();
        
        $firstName = $this->stripTagsInputRequest('firstName');
        $lastName = $this->stripTagsInputRequest('lastName');
        
        $service->execute($this->userId(), $firstName, $lastName);
        
        $viewService = $this->buildViewService();
        $user = $viewService->showById($this->userId());
        return $this->singleQueryResponse($this->arrayDataOfUser($user));
    }
    
    public function changePassword()
    {
        $service = $this->buildChangePasswordService();
        
        $previousPassword = $this->stripTagsInputRequest('previousPassword');
        $newPassword = $this->stripTagsInputRequest('newPassword');
        
        $service->execute($this->userId(), $previousPassword, $newPassword);
        
        return $this->commandOkResponse();
    }
    
    protected function arrayDataOfUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];
    }
    protected function buildViewService()
    {
        $userRepository = $this->em->getRepository(User::class);
        return new ViewUser($userRepository);
    }
    protected function buildChangeProfileService()
    {
        $userRepository = $this->em->getRepository(User2::class);
        return new ChangeUserProfile($userRepository);
    }
    protected function buildChangePasswordService()
    {
        $userRepository = $this->em->getRepository(User2::class);
        return new ChangeUserPassword($userRepository);
    }
}
