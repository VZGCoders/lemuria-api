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

use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;

class MessageDelete extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        $message = null;

        if (! isset($data->guild_id)) {
            if ($channel = $this->lemuria->private_channels->get('id', $data->channel_id)) {
                $message = $channel->messages->pull($data->id);
            }
        } else {
            if ($guild = $this->lemuria->guilds->get('id', $data->guild_id)) {
                if ($channel = $guild->channels->get('id', $data->channel_id)) {
                    $message = $channel->messages->pull($data->id);
                }
            }
        }

        $deferred->resolve(is_null($message) ? $data : $message);
    }
}
