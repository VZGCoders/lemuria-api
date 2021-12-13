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
use Lemuria\WebSockets\Event;

class ThreadMemberUpdate extends Event
{
    public function handle(Deferred &$deferred, $data)
    {
        $member = $this->factory->create(Member::class, $data, true);
        $guild = $this->lemuria->guilds->get('id', $data->guild_id);

        foreach ($guild->channels as $channel) {
            if ($thread = $channel->threads->get('id', $data->id)) {
                $thread->members->push($member);
                break;
            }
        }

        $deferred->resolve($member);
    }
}
