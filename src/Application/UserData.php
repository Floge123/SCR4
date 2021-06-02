<?php

namespace Application;

class UserData
{
    public function __construct(
        private string $userName
    ) {
    }

    public function getUserName(): string
    {
        return $this->userName;
    }
}
