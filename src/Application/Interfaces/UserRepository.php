<?php

namespace Application\Interfaces;

interface UserRepository {
    public function getUser(string $userName): ?\Application\Entities\User;

    public function registerUser(string $userName, string $password): void;
    public function getUserForUserNameAndPassword(string $userName, string $password): ?\Application\Entities\User;
}