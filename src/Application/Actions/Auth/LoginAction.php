<?php
declare(strict_types=1);

namespace Php\Application\Actions\Auth;

use League\Plates\Engine;
use Php\Application\Middlewares\LoginAuth;
use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;

final class LoginAction extends AuthAction
{
    private UserRepository $userRepo;

    public function __construct(Engine $templates, Messages $flash, UserRepository $userRepo)
    {
        parent::__construct($templates, $flash);
        $this->userRepo = $userRepo;
    }

    protected function action(): Response
    {
        $params = $this->request->getParsedBody();

        $invalid = false;
        if (empty($userName = $params['userName'])) {
            $invalid = true;
            $this->flash->addMessage('errors', 'Require username.');
        }
        if (empty($password = $params['password'])) {
            $invalid = true;
            $this->flash->addMessage('errors', 'Require password.');
        }
        if ($invalid) {
            return $this->redirectBack($this->request, $this->response);
        }

        $user = $this->userRepo->findByNameAndPassword($userName, $password);
        if (is_null($user)) {
            $this->flash->addMessage('errors', 'Invalid username or password.');
            return $this->redirectBack($this->request, $this->response);
        }

        return $this->response
            ->withAddedHeader('Set-Cookie', sprintf('%s=%d', LoginAuth::SESSION_KEY, $user->id))
            ->withAddedHeader('Location', $this->request->getServerParams()['HTTP_REFERER'])
            ->withStatus(302);
    }
}
