<?php

namespace Application\Services;

class AuthenticationService {
    const SESSION_USER_NAME = "userName";

    public function __construct(private \Application\Interfaces\Session $session) {

    }

    public function getUserName(): ?string {
        return $this->session->get(self::SESSION_USER_NAME);
    }

    public function signIn(string $userName): void {
        $this->session->put(self::SESSION_USER_NAME, $userName);
    }

    public function signOut(): void {
        $this->session->delete(self::SESSION_USER_NAME);
    }
}