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

use Lemuria\Parts\WebSockets\PresenceUpdate as PresenceUpdatePart;
use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;

class PresenceUpdate extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        /**
         * @var PresenceUpdatePart
         */
        $presence = $this->factory->create(PresenceUpdatePart::class, $data, true);

        if ($guild = $presence->guild) {
            if ($member = $presence->member) {
                $oldPresence = $member->updateFromPresence($presence);

                $guild->members->offsetSet($member->id, $member);
                $this->lemuria->guilds->offsetSet($guild->id, $guild);

                $deferred->resolve([$presence, $oldPresence]);
            }
        }

        $deferred->resolve($presence);
    }
}
