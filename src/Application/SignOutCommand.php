<?php

namespace Application;

class SignOutCommand {
    public function __construct(
        private Services\AuthenticationService $authenticationService,
        private Interfaces\UserRepository $userRepository
    ) {

    }

    public function execute(): void {
        $this->authenticationService->signOut();
    }
}