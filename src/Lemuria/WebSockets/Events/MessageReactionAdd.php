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

use Lemuria\Parts\WebSockets\MessageReaction;
use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;
use Lemuria\Parts\Channel\Reaction;

class MessageReactionAdd extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        $reaction = new MessageReaction($this->lemuria, (array) $data, true);

        if ($channel = $reaction->channel) {
            if ($message = $channel->messages->offsetGet($reaction->message_id)) {
                $addedReaction = false;

                foreach ($message->reactions as $react) {
                    if ($react->id == $reaction->reaction_id) {
                        ++$react->count;

                        if ($reaction->discord_id == $this->lemuria->id) {
                            $react->me = true;
                        }

                        $addedReaction = true;
                        break;
                    }
                }

                // New reaction added
                if (! $addedReaction) {
                    $message->reactions->push($message->reactions->create([
                        'count' => 1,
                        'me' => $reaction->discord_id == $this->lemuria->id,
                        'emoji' => $reaction->emoji->getRawAttributes(),
                    ], true));
                }
            }
        }

        $deferred->resolve($reaction);
    }
}
