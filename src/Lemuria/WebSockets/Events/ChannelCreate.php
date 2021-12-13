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

class ChannelCreate extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        /** @var Channel */
        $channel = $this->factory->create(Channel::class, $data, true);

        if ($channel->is_private) {
            $this->lemuria->private_channels->push($channel);
        } else {
            if ($guild = $this->lemuria->guilds->get('id', $channel->guild_id)) {
                $guild->channels->push($channel);
                $this->lemuria->guilds->push($guild);
            }
        }

        $deferred->resolve($channel);
    }
}
