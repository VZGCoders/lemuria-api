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

use Lemuria\Helpers\Deferred;
use Lemuria\Parts\Interactions\Interaction;
use Lemuria\WebSockets\Event;

class InteractionCreate extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data): void
    {
        // do nothing with interactions - pass on to LemuriaPHP-Slash
        $deferred->resolve($this->factory->create(Interaction::class, $data, true));
    }
}
