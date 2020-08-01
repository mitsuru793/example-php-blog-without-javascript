<?php
declare(strict_types=1);

namespace Php\Application\Actions\Post;

use League\Plates\Engine;
use Php\Domain\Post\PostRepository;
use Php\Domain\Post\PostTransformer;
use Php\Domain\Tag\TagRepository;
use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;

final class ShowPostAction extends PostAction
{
    private UserRepository $userRepository;

    private PostRepository $postRepo;

    private PostTransformer $postTransformer;

    private TagRepository $tagRepo;

    public function __construct(Engine $templates, Messages $flash, PostRepository $postRepo, PostTransformer $postTransformer, UserRepository $userRepository, TagRepository $tagRepo)
    {
        parent::__construct($templates, $flash);
        $this->userRepository = $userRepository;
        $this->postRepo = $postRepo;
        $this->postTransformer = $postTransformer;
        $this->tagRepo = $tagRepo;
    }

    /**
     * @throws \League\Route\Http\Exception\BadRequestException
     */
    protected function action(): Response
    {
        $postId = (int)$this->resolveArg('postId');
        $post = $this->postRepo->find($postId);
        if (is_null($post)) {
            $this->flash->addMessage('errors', 'Not found post.');
            return $this->renderView($this->response, 'post/show', compact('post'));
        }
        $post = $this->tagRepo->findByPost($post);

        return $this->renderView($this->response, 'post/show', compact('post'));
    }
}
