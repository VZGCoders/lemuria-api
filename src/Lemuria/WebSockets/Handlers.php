<?php

/*
 * This file is a part of the LemuriaPHP project.
 *
 * Copyright (c) 2015-present David Cole <david.cole1340@gmail.com>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Lemuria\WebSockets;

/**
 * This class contains all the handlers for the individual WebSocket events.
 */
class Handlers
{
    /**
     * An array of handlers.
     *
     * @var array Array of handlers.
     */
    protected $handlers = [];

    /**
     * Constructs the list of handlers.
     */
    public function __construct()
    {
        // General
        $this->addHandler(Event::PRESENCE_UPDATE, \Lemuria\WebSockets\Events\PresenceUpdate::class);
        $this->addHandler(Event::TYPING_START, \Lemuria\WebSockets\Events\TypingStart::class);
        $this->addHandler(Event::VOICE_STATE_UPDATE, \Lemuria\WebSockets\Events\VoiceStateUpdate::class);
        $this->addHandler(Event::VOICE_SERVER_UPDATE, \Lemuria\WebSockets\Events\VoiceServerUpdate::class);
        $this->addHandler(Event::INTERACTION_CREATE, \Lemuria\WebSockets\Events\InteractionCreate::class);

        // Guild Event handlers
        $this->addHandler(Event::GUILD_CREATE, \Lemuria\WebSockets\Events\GuildCreate::class);
        $this->addHandler(Event::GUILD_DELETE, \Lemuria\WebSockets\Events\GuildDelete::class);
        $this->addHandler(Event::GUILD_UPDATE, \Lemuria\WebSockets\Events\GuildUpdate::class);
        $this->addHandler(Event::GUILD_INTEGRATIONS_UPDATE, \Lemuria\WebSockets\Events\GuildIntegrationsUpdate::class);

        // Invite handlers
        $this->addHandler(Event::INVITE_CREATE, \Lemuria\WebSockets\Events\InviteCreate::class);
        $this->addHandler(Event::INVITE_DELETE, \Lemuria\WebSockets\Events\InviteDelete::class);

        // Channel Event handlers
        $this->addHandler(Event::CHANNEL_CREATE, \Lemuria\WebSockets\Events\ChannelCreate::class);
        $this->addHandler(Event::CHANNEL_UPDATE, \Lemuria\WebSockets\Events\ChannelUpdate::class);
        $this->addHandler(Event::CHANNEL_DELETE, \Lemuria\WebSockets\Events\ChannelDelete::class);
        $this->addHandler(Event::CHANNEL_PINS_UPDATE, \Lemuria\WebSockets\Events\ChannelPinsUpdate::class);

        // Ban Event handlers
        $this->addHandler(Event::GUILD_BAN_ADD, \Lemuria\WebSockets\Events\GuildBanAdd::class);
        $this->addHandler(Event::GUILD_BAN_REMOVE, \Lemuria\WebSockets\Events\GuildBanRemove::class);

        // Message handlers
        $this->addHandler(Event::MESSAGE_CREATE, \Lemuria\WebSockets\Events\MessageCreate::class, ['message']);
        $this->addHandler(Event::MESSAGE_DELETE, \Lemuria\WebSockets\Events\MessageDelete::class);
        $this->addHandler(Event::MESSAGE_DELETE_BULK, \Lemuria\WebSockets\Events\MessageDeleteBulk::class);
        $this->addHandler(Event::MESSAGE_UPDATE, \Lemuria\WebSockets\Events\MessageUpdate::class);
        $this->addHandler(Event::MESSAGE_REACTION_ADD, \Lemuria\WebSockets\Events\MessageReactionAdd::class);
        $this->addHandler(Event::MESSAGE_REACTION_REMOVE, \Lemuria\WebSockets\Events\MessageReactionRemove::class);
        $this->addHandler(Event::MESSAGE_REACTION_REMOVE_ALL, \Lemuria\WebSockets\Events\MessageReactionRemoveAll::class);
        $this->addHandler(Event::MESSAGE_REACTION_REMOVE_EMOJI, \Lemuria\WebSockets\Events\MessageReactionRemoveEmoji::class);

        // New Member Event handlers
        $this->addHandler(Event::GUILD_MEMBER_ADD, \Lemuria\WebSockets\Events\GuildMemberAdd::class);
        $this->addHandler(Event::GUILD_MEMBER_REMOVE, \Lemuria\WebSockets\Events\GuildMemberRemove::class);
        $this->addHandler(Event::GUILD_MEMBER_UPDATE, \Lemuria\WebSockets\Events\GuildMemberUpdate::class);

        // New Role Event handlers
        $this->addHandler(Event::GUILD_ROLE_CREATE, \Lemuria\WebSockets\Events\GuildRoleCreate::class);
        $this->addHandler(Event::GUILD_ROLE_DELETE, \Lemuria\WebSockets\Events\GuildRoleDelete::class);
        $this->addHandler(Event::GUILD_ROLE_UPDATE, \Lemuria\WebSockets\Events\GuildRoleUpdate::class);

        // Thread events
        $this->addHandler(Event::THREAD_CREATE, \Lemuria\WebSockets\Events\ThreadCreate::class);
        $this->addHandler(Event::THREAD_UPDATE, \Lemuria\WebSockets\Events\ThreadUpdate::class);
        $this->addHandler(Event::THREAD_DELETE, \Lemuria\WebSockets\Events\ThreadDelete::class);
        $this->addHandler(Event::THREAD_LIST_SYNC, \Lemuria\WebSockets\Events\ThreadListSync::class);
        $this->addHandler(Event::THREAD_MEMBER_UPDATE, \Lemuria\WebSockets\Events\ThreadMemberUpdate::class);
        $this->addHandler(Event::THREAD_MEMBERS_UPDATE, \Lemuria\WebSockets\Events\ThreadMembersUpdate::class);
    }

    /**
     * Adds a handler to the list.
     *
     * @param string $event        The WebSocket event name.
     * @param string $classname    The Event class name.
     * @param array  $alternatives Alternative event names for the handler.
     */
    public function addHandler(string $event, string $classname, array $alternatives = []): void
    {
        $this->handlers[$event] = [
            'class' => $classname,
            'alternatives' => $alternatives,
        ];
    }

    /**
     * Returns a handler.
     *
     * @param string $event The WebSocket event name.
     *
     * @return array|null The Event class name or null;
     */
    public function getHandler(string $event): ?array
    {
        if (isset($this->handlers[$event])) {
            return $this->handlers[$event];
        }

        return null;
    }

    /**
     * Returns the handlers array.
     *
     * @return array Array of handlers.
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * Returns the handlers.
     *
     * @return array Array of handler events.
     */
    public function getHandlerKeys(): array
    {
        return array_keys($this->handlers);
    }

    /**
     * Removes a handler.
     *
     * @param string $event The event handler to remove.
     */
    public function removeHandler(string $event): void
    {
        unset($this->handlers[$event]);
    }
}
