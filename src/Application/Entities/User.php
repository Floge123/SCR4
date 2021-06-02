<?php

namespace Application\Entities;

class User {
    public function __construct(
        private string $userName
    ) {
    }

    public function getUserName(): string
    {
        return $this->userName;
    }
}