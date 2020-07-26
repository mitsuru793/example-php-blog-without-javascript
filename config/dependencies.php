<?php
declare(strict_types=1);

return function (\League\Container\Container $c) {
    $c->add(\Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB::class, function () {
        if (strpos(getenv('APP_ENV'), 'test') !== false) {
            $pdo = new \PDO(
                sprintf('mysql:host=%s;dbname=%s', getenv('TEST_DB_HOST'), getenv('TEST_DB_DATABASE')),
                getenv('TEST_DB_USER'),
                getenv('TEST_DB_PASS'),
            );
        } else {
            $pdo = new \PDO(
                sprintf('mysql:host=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_DATABASE')),
                getenv('DB_USER'),
                getenv('DB_PASS'),
            );
        }
        return new \Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB($pdo, 'mysql');
    }, true);

    $c->add(\League\Plates\Engine::class, function () {
        return League\Plates\Engine::create(__DIR__ . '/../src/Template');
    }, true);

    $c->add(\Abraham\TwitterOAuth\TwitterOAuth::class, function () {
        return new \Abraham\TwitterOAuth\TwitterOAuth(
            getenv('TWITTER_CONSUMER_KEY'),
            getenv('TWITTER_CONSUMER_SECRET'),
            getenv('TWITTER_CONSUMER_ACCESS_TOKEN'),
            getenv('TWITTER_CONSUMER_ACCESS_TOKEN_SECRET'),
        );
    }, true);
};
