<?php

namespace AppBundle\Composer;

use Composer\Script\Event;

class HerokuDatabase
{
    /**
     * Script executed previous installation command of Composer.
     * It allows the application to setup database parameters
     * importing them from environment variables
     * 
     * @param Event $event
     */
    public static function populateEnvironment(Event $event)
    {
        $url = getenv("DATABASE_URL");

        if ($url) {
            $url = parse_url($url);
            putenv("DATABASE_HOST={$url['host']}");
            putenv("DATABASE_USER={$url['user']}");
            putenv("DATABASE_PASSWORD={$url['pass']}");
            $db = substr($url['path'],1);
            putenv("DATABASE_NAME={$db}");
        }

        $io = $event->getIO();

        $io->write("DATABASE_URL=".getenv("DATABASE_URL"));
    }
}