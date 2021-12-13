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

class MessageReactionRemoveEmoji extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        if ($channel = $this->lemuria->getChannel($data->channel_id)) {
            if ($message = $channel->messages->offsetGet($data->message_id)) {
                foreach ($message->reactions as $key => $react) {
                    if ($react->id == $data->id) {
                        unset($message->reactions[$key]);
                    }
                }
            }
        }

        $deferred->resolve($data);
    }
}
