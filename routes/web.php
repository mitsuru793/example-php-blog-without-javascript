<?php
declare(strict_types=1);

use Php\Application\Actions\Auth;
use Php\Application\Actions\Debug;
use Php\Application\Actions\Post;
use Php\Application\Actions\Seed;
use Php\Application\Actions\Tweet;
use Php\Application\Actions\Twitter;
use Php\Application\Actions\UIFacesUser;

return function (League\Route\Router $router, \Psr\Container\ContainerInterface $container) {
    $router->get('/', Post\ListPostsAction::class);

    $router->post('/login', Auth\LoginAction::class);
    $router->post('/logout', Auth\LogoutAction::class);

    $router->group('/posts', function (\League\Route\RouteGroup $r) {
        $r->get('/{postId}', Post\ShowPostAction::class);
        $r->get('/{postId}/edit', Post\EditPostAction::class);
        $r->put('/{postId}', Post\UpdatePostAction::class);
    });

    // TODO There is no flow for the following routing.
    $router->group('/seeds', function (\League\Route\RouteGroup $r) {
        $r->post('/', Seed\StoreSeedAction::class);
    });
};
