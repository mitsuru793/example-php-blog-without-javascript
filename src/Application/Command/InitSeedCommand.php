<?php
declare(strict_types=1);

namespace Php\Application\Command;

use Php\Domain\Post\PostRepository;
use Php\Domain\Tag\TagRepository;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;

final class InitSeedCommand
{
    private UserRepository $userRepository;

    private PostRepository $postRepository;

    private TagRepository $tagRepository;

    private ExtendedEasyDB $db;

}