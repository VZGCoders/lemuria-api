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

use Lemuria\Parts\Guild\Guild;
use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;

class GuildDelete extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        $guild = $this->lemuria->guilds->get('id', $data->id);

        if (! $guild) {
            $guild = $this->factory->create(Guild::class, $data, true);
        }

        $this->lemuria->guilds->pull($guild->id);

        $deferred->resolve([$guild, $data->unavailable ?? false]);
    }
}
