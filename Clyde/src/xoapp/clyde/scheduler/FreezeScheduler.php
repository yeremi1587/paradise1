<?php

namespace xoapp\clyde\scheduler;

use pocketmine\scheduler\Task;
use xoapp\clyde\session\SessionFactory;

class FreezeScheduler extends Task
{

    public function onRun(): void
    {
        $f_sessions = SessionFactory::getInstance()->getFreezeSessions();

        foreach ($f_sessions as $f_session) {
            $f_session->update();
        }
    }
}