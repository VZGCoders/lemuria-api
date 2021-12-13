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

use Lemuria\Parts\Channel\Channel;
use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;

class ChannelDelete extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        $channel = $this->factory->create(Channel::class, $data);

        if ($guild = $channel->guild) {
            $guild->channels->pull($channel->id);

            $this->lemuria->guilds->push($guild);
        }

        $deferred->resolve($channel);
    }
}
