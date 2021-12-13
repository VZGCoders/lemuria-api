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

use Lemuria\Parts\Guild\Role;
use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;

class GuildRoleUpdate extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        $adata = (array) $data->role;
        $adata['guild_id'] = $data->guild_id;

        $rolePart = $this->factory->create(Role::class, $adata, true);

        if ($guild = $this->lemuria->guilds->get('id', $rolePart->guild_id)) {
            $old = $guild->roles->get('id', $rolePart->id);
            $guild->roles->push($rolePart);

            $this->lemuria->guilds->push($guild);
        } else {
            $old = null;
        }

        $deferred->resolve([$rolePart, $old]);
    }
}
