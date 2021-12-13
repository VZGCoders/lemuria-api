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

use Lemuria\Parts\User\Member;
use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;

class GuildMemberRemove extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        $member = $this->factory->create(Member::class, $data, true);

        if ($guild = $this->lemuria->guilds->get('id', $member->guild_id)) {
            $guild->members->pull($member->user->id);
            --$guild->member_count;

            $this->lemuria->guilds->push($guild);
        }

        $deferred->resolve($member);
    }
}
