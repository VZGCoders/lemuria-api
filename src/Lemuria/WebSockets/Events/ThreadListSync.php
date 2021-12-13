<?php

/*
 * This file is a part of the LemuriaPHP project.
 *
 * Copyright (c) 2015-present David Cole <david.cole1340@gmail.com>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Lemuria\WebSockets\Events;

use Lemuria\Helpers\Deferred;
use Lemuria\Parts\Thread\Member;
use Lemuria\Parts\Thread\Thread;
use Lemuria\WebSockets\Event;

class ThreadListSync extends Event
{
    public function handle(Deferred &$deferred, $data)
    {
        $guild = $this->lemuria->guilds->get('id', $data->guild_id);
        $members = (array) $data->members;

        foreach ($data->threads as $thread) {
            /** @var Thread */
            $thread = $this->factory->create(Thread::class, $thread, true);

            foreach ($members as $member) {
                if ($member->id == $thread->id) {
                    $thread->members->push($this->factory->create(Member::class, $members[$thread->id], true));
                    break;
                }
            }

            $guild->threads->push($thread);
        }

        $deferred->resolve();
    }
}
