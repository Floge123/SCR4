<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once($file);
    }
});


$sp = new \ServiceProvider();

// ----- APPLICATION
//command and queries
$sp->register(\Application\ProductQuery::class);
$sp->register(\Application\ProductSearchQuery::class);
$sp->register(\Application\SignedInUserQuery::class);

$sp->register(\Application\SignInCommand::class);
$sp->register(\Application\SignOutCommand::class);
$sp->register(\Application\RegisterCommand::class);
$sp->register(\Application\AddProductCommand::class);


//services
$sp->register(\Application\Services\AuthenticationService::class);

// ----- INFRASTRUCTURE
$sp->register(\Infrastructure\Session::class, isSingleton: true);
$sp->register(\Application\Interfaces\Session::class, \Infrastructure\Session::class);

$sp->register(\Infrastructure\Repository::class, function() {
    return new \Infrastructure\Repository('localhost', 'root', '', 'produktbewertungsportal');
}, isSingleton: true);
$sp->register(\Application\Interfaces\ProductRepository::class, \Infrastructure\Repository::class);
$sp->register(\Application\Interfaces\UserRepository::class, \Infrastructure\Repository::class);

// ----- PRESENTATION
//MVC framework
$sp->register(\Presentation\MVC\MVC::class, function(){
    return new \Presentation\MVC\MVC();
}, isSingleton: true);

//controllers
$sp->register(\Presentation\Controllers\Home::class);
$sp->register(\Presentation\Controllers\Products::class);
$sp->register(\Presentation\Controllers\User::class);


// === handle request
$sp->resolve(\Presentation\MVC\MVC::class)->handleRequest($sp);

