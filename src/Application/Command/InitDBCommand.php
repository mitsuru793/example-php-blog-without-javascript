<?php
declare(strict_types=1);

namespace Php\Application\Command;

use Faker\Factory;
use Php\Domain\Post\Post;
use Php\Domain\Post\PostRepository;
use Php\Domain\Tag\Tag;
use Php\Domain\Tag\TagRepository;
use Php\Domain\User\User;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InitDBCommand extends Command
{
    protected static $defaultName = 'init:db';

    private ExtendedEasyDB $db;

    private UserRepository $userRepository;

    private PostRepository $postRepository;

    private TagRepository $tagRepository;

    public function __construct(ExtendedEasyDB $db, UserRepository $userRepository, PostRepository $postRepository, TagRepository $tagRepository)
    {
        $this->db = $db;
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->db->runSqlFile(self::ROOT . '/config/create_tables.sql');
        $this->makeSeed();
        return Command::SUCCESS;
    }

    private function makeSeed(): void
    {
        $faker = Factory::create();

        $users = array_map(fn ($i) => new User(
            null, $faker->name, $faker->word
        ), range(1, 30));
        $this->userRepository->createMany($users);
        $users = $this->userRepository->paging(1, 30);

        $tags = array_map(fn ($i) => new Tag(
            null, $faker->word
        ), range(1, 50));
        $this->tagRepository->createMany($tags);

        for ($i = 0; $i < 200; $i++) {
            $user = collect($users)->random();
            $content = implode('', $faker->sentences(5));
            $post = new Post(null, $user, $faker->sentence, $content, (int)$faker->year);
            $this->postRepository->create($post);

            $count = random_int(0, 3);
            $tags = $this->tagRepository->findRandoms($count);
            $this->postRepository->updateTags($post->id, $tags);
        }
    }
}
