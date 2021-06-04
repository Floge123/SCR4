<?php

namespace Presentation\Controllers;

class User extends \Presentation\MVC\Controller {
    const PARAM_USER_NAME = 'un';
    const PARAM_PASSWORD = 'pwd';
    const PARAM_PASSWORD_CONFIRM = 'pwd_conf';
    const PARAM_CONTEXT = 'ctx';

    public function __construct(
        private \Application\SignedInUserQuery $signedInUserQuery,
        private \Application\SignInCommand $signInCommand,
        private \Application\SignOutCommand $signOutCommand,
        private \Application\RegisterCommand $registerCommand
    ) {
    }

    public function GET_LogIn(): \Presentation\MVC\ActionResult {
        $user = $this->signedInUserQuery->execute();
        if ($user != null) {
            return $this->redirect('Home', 'Index');
        }
        return $this->view('login', [
            'user' => $user,
            'context' => $this->getRequestUri(),
            'userName' => $this->tryGetParam(self::PARAM_USER_NAME, $value) ? $value : ''
        ]);
    }

    public function POST_LogIn(): \Presentation\MVC\ActionResult {
        if (!$this->signInCommand->execute($this->getParam(self::PARAM_USER_NAME), $this->getParam(self::PARAM_PASSWORD))) {
            return $this->view('login', [
                'user' => $this->signedInUserQuery->execute(),
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'userName' => $this->getParam(self::PARAM_USER_NAME),
                'errors' => ['Invalid user name or password.']
            ]);
        }
        return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
    }

    public function GET_Register(): \Presentation\MVC\ActionResult {
        $user = $this->signedInUserQuery->execute();
        if ($user != null) {
            return $this->redirect('Home', 'Index');
        }
        return $this->view('register', [
            'user' => $user,
            'context' => $this->getRequestUri(),
            'userName' => $this->tryGetParam(self::PARAM_USER_NAME, $value) ? $value : ''
        ]);
    }

    public function POST_Register(): \Presentation\MVC\ActionResult {
        $errors = [];
        $this->tryGetParam(self::PARAM_USER_NAME, $userName);
        $this->tryGetParam(self::PARAM_PASSWORD, $password);
        $this->tryGetParam(self::PARAM_PASSWORD_CONFIRM, $confpassword);
        if (strlen($userName <= 0)) {
            $errors[] = 'Enter a username.';
        }
        if (strlen($password) <= 0) {
            $errors[] = 'Enter a password.';
        }
        if ($password != $confpassword) {
            $errors[] = 'Passwords do not match.';
        }
        if (!$this->registerCommand->execute(
            $userName, $password, $confpassword
        )) {
            $errors[] = 'User already exists.';
        }

        if (sizeof($errors) != 0) {
            return $this->view('register', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => $userName,
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'errors' => $errors
            ]);
        }

        return $this->POST_LogIn();
    }

    public function POST_LogOut(): \Presentation\MVC\ActionResult {
       $this->signOutCommand->execute();
       return $this->redirect('Home', 'Index');
    }
}
