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
use Lemuria\Parts\Guild\Ban;
use Lemuria\Parts\Guild\Guild;
use Lemuria\Parts\Guild\Role;
use Lemuria\Parts\User\Member;
use Lemuria\Parts\User\User;
use Lemuria\Parts\WebSockets\VoiceStateUpdate as VoiceStateUpdatePart;
use Lemuria\WebSockets\Event;
use Lemuria\Helpers\Deferred;
use Lemuria\Endpoint;
use Lemuria\Parts\Thread\Member as ThreadMember;
use Lemuria\Parts\Thread\Thread;

class GuildCreate extends Event
{
    /**
     * @inheritdoc
     */
    public function handle(Deferred &$deferred, $data)
    {
        if (isset($data->unavailable) && $data->unavailable) {
            $deferred->reject(['unavailable', $data->id]);

            return $deferred->promise();
        }

        /** @var Guild */
        $guildPart = $this->factory->create(Guild::class, $data, true);
        foreach ($data->roles as $role) {
            $role = (array) $role;
            $role['guild_id'] = $guildPart->id;
            $rolePart = $this->factory->create(Role::class, $role, true);

            $guildPart->roles->offsetSet($rolePart->id, $rolePart);
        }

        foreach ($data->channels as $channel) {
            $channel = (array) $channel;
            $channel['guild_id'] = $data->id;
            $channelPart = $this->factory->create(Channel::class, $channel, true);

            $guildPart->channels->offsetSet($channelPart->id, $channelPart);
        }

        foreach ($data->members as $member) {
            $member = (array) $member;
            $member['guild_id'] = $data->id;

            if (! $this->lemuria->users->has($member['user']->id)) {
                $userPart = $this->factory->create(User::class, $member['user'], true);
                $this->lemuria->users->offsetSet($userPart->id, $userPart);
            }

            $memberPart = $this->factory->create(Member::class, $member, true);
            $guildPart->members->offsetSet($memberPart->id, $memberPart);
        }

        foreach ($data->presences as $presence) {
            if ($member = $guildPart->members->offsetGet($presence->user->id)) {
                $member->fill((array) $presence);
                $guildPart->members->offsetSet($member->id, $member);
            }
        }

        foreach ($data->voice_states as $state) {
            if ($channel = $guildPart->channels->offsetGet($state->channel_id)) {
                $state = (array) $state;
                $state['guild_id'] = $guildPart->id;

                $stateUpdate = $this->factory->create(VoiceStateUpdatePart::class, $state, true);

                $channel->members->offsetSet($stateUpdate->discord_id, $stateUpdate);
                $guildPart->channels->offsetSet($channel->id, $channel);
            }
        }

        foreach ($data->threads as $rawThread) {
            /**
             * @var Thread
             */
            $thread = $this->factory->create(Thread::class, $rawThread, true);

            if ($rawThread->member ?? null) {
                $member = (array) $rawThread->member;
                $member['id'] = $thread->id;
                $member['discord_id'] = $this->lemuria->id;

                $selfMember = $this->factory->create(ThreadMember::class, $member, true);
                $thread->members->push($selfMember);
            }

            $guildPart->channels->get('id', $thread->parent_id)->threads->push($thread);
        }

        $resolve = function () use (&$guildPart, $deferred) {
            if ($guildPart->large || $guildPart->member_count > $guildPart->members->count()) {
                $this->lemuria->addLargeGuild($guildPart);
            }

            $this->lemuria->guilds->offsetSet($guildPart->id, $guildPart);

            $deferred->resolve($guildPart);
        };

        if ($this->lemuria->options['retrieveBans']) {
            $this->http->get(Endpoint::bind(Endpoint::GUILD_BANS, $guildPart->id))->done(function ($rawBans) use (&$guildPart, $resolve) {
                foreach ($rawBans as $ban) {
                    $ban = (array) $ban;
                    $ban['guild'] = $guildPart;

                    $banPart = $this->factory->create(Ban::class, $ban, true);

                    $guildPart->bans->offsetSet($banPart->id, $banPart);
                }

                $resolve();
            }, $resolve);
        } else {
            $resolve();
        }
    }
}
