<?php

namespace Application;

class SignedInUserQuery
{
    public function __construct(
        private Services\AuthenticationService $authenticationService,
        private Interfaces\UserRepository $userRepository
    ) {
    }
    public function execute(): ?UserData
    {
        $userName = $this->authenticationService->getUserName();
        if ($userName === null) {
          return null;
        }
        $user = $this->userRepository->getUser($userName);
        if ($user === null) {
            return null;
        }
        return new UserData($user->getUserName());
    }
}
