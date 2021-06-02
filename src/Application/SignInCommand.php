<?php

namespace Application;

class SignInCommand {
    public function __construct(
        private Services\AuthenticationService $authenticationService,
        private Interfaces\UserRepository $userRepository
    ) {

    }

    public function execute(string $userName, string $password): bool {
        $this->authenticationService->signOut();
        $user = $this->userRepository->getUserForUserNameAndPassword($userName, $password);
        if ($user != null) {
            $this->authenticationService->signIn($user->getUserName());
            return true;
        }
        return false;
    }
}