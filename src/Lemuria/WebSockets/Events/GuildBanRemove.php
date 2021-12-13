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

use Lemuria\Parts\Guild\Ban;
use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;

class GuildBanRemove extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        $ban = $this->factory->create(Ban::class, $data, true);

        if ($guild = $ban->guild) {
            $guild->bans->pull($ban->user->id);
            $this->lemuria->guilds->push($guild);
        }

        $deferred->resolve($ban);
    }
}
