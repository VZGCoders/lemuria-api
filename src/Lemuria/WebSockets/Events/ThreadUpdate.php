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
use Lemuria\WebSockets\Event;

class ThreadUpdate extends Event
{
    public function handle(Deferred &$deferred, $data)
    {
        $thread = null;

        if ($guild = $this->lemuria->guilds->get('id', $data->guild_id)) {
            if ($parent = $guild->channels->get('id', $data->parent_id)) {
                if ($thread = $parent->threads->get('id', $data->id)) {
                    $thread->fill((array) $data);
                }
            }
        }

        $deferred->resolve($thread);
    }
}
