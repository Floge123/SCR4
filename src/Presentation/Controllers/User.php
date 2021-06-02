<?php

namespace Presentation\Controllers;

class User extends \Presentation\MVC\Controller {
    const PARAM_USER_NAME = 'un';
    const PARAM_PASSWORD = 'pwd';
    const PARAM_PASSWORD_CONFIRM = 'pwd_conf';

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
            return $this->redirect('Home', 'Index'); //Besser, wenn man auf die vorherige Seite kommt
        }
        return $this->view('login', [
            'user' => $user,
            'userName' => $this->tryGetParam(self::PARAM_USER_NAME, $value) ? $value : ''
        ]);
    }

    public function POST_LogIn(): \Presentation\MVC\ActionResult {
        if (!$this->signInCommand->execute($this->getParam(self::PARAM_USER_NAME), $this->getParam(self::PARAM_PASSWORD))) {
            return $this->view('login', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => $this->getParam(self::PARAM_USER_NAME),
                'errors' => ['Invalid user name or password.']
            ]);
        }
        return $this->redirect('Home', 'Index'); //do better
    }

    public function GET_Register(): \Presentation\MVC\ActionResult {
        $user = $this->signedInUserQuery->execute();
        if ($user != null) {
            return $this->redirect('Home', 'Index');
        }
        return $this->view('register', [
            'user' => $user,
            'userName' => $this->tryGetParam(self::PARAM_USER_NAME, $value) ? $value : ''
        ]);
    }

    public function POST_Register(): \Presentation\MVC\ActionResult {
        if ($this->getParam(self::PARAM_PASSWORD) != $this->getParam(self::PARAM_PASSWORD_CONFIRM)) {
            return $this->view('register', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => $this->getParam(self::PARAM_USER_NAME),
                'errors' => ['Passwords do not match.']
            ]);
        }

        if (!$this->registerCommand->execute(
            $this->getParam(self::PARAM_USER_NAME),
            $this->getParam(self::PARAM_PASSWORD),
            $this->getParam(self::PARAM_PASSWORD_CONFIRM)
        )) {
            return $this->view('register', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => $this->getParam(self::PARAM_USER_NAME),
                'errors' => ['User already exists.']
            ]);
        }
        return $this->POST_LogIn();
    }

    public function POST_LogOut(): \Presentation\MVC\ActionResult {
       $this->signOutCommand->execute();
       return $this->redirect('Home', 'Index');
    }
}