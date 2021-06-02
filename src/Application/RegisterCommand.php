<?php

namespace Application;

class RegisterCommand {
    public function __construct(
        private Services\AuthenticationService $authenticationService,
        private Interfaces\UserRepository $userRepository
    ) {

    }

    public function execute(string $userName, string $password, string $passwordConfirm): bool {
        $this->authenticationService->signOut();
        $user = $this->userRepository->getUserForUserNameAndPassword($userName, $password);
        if ($user == null) {
            $this->userRepository->registerUser($userName, $password);
            return true;
        }
        return false;
    }
}