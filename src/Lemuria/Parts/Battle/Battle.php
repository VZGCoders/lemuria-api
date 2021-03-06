<?php

/*
 * This file is a part of the Lemuria project.
 *
 * Copyright (c) 2021-present Valithor Obsidion <valzargaming@gmail.com>
 */

namespace Lemuria\Parts\Battle;

use Lemuria\Endpoint;
use Lemuria\Parts\Part;
use React\Promise\ExtendedPromiseInterface;

/**
 * A battle is a general battle that is not attached to a group.
 *

 * @property int    $id            The unique identifier of the battle.
 * @property int    $party_id      Participating Party id.
 * @property bool   $active        Whether the battle is active.
 * @property string $status        Placeholder.
 * @property int    $turn          Turn number.
 *
 * @property Enemy  $enemy1        The Enemy the Party is fighting.
 *
 * @property        $player1act    The action that will be taken by player1 on their turn.
 * @property        $player2act    The action that will be taken by player2 on their turn.
 * @property        $player3act    The action that will be taken by player3 on their turn.
 * @property        $player4act    The action that will be taken by player4 on their turn.
 * @property        $player5act    The action that will be taken by player5 on their turn.
 *
 * @property TimerInterface $timer Controls the flow of battle. //Declared with $this->timer=addPeriodicTimer($int, function ($timer) ...) and nulled with cancelTimer($this->timer).
 */
class Battle extends Part
{

    /**
     * @inheritdoc
     */
    protected static $fillable = ['id', 'party_id', 'active', 'status', 'turn'];

	/**
     * Returns the fillable attributes.
     *
     * @return array
     */
    public static function getFillableAttributes($context = '')
	{
		$fillable = array();
		foreach (self::$fillable as $attr) {
			if (! $context || in_array($context, self::$fillable)) {
				$fillable[] = $attr;
			}
		}
		return $fillable;
	}

	/**
     * @inheritdoc
     */
    public function getCreatableAttributes(): array
    {
        return [
            'id' => $this->id,
			'party_id' => $this->party_id,
			'active' => $this->active,
			'status' => $this->status,
			'turn' => $this->turn,
        ];
    }
	

    /**
     * @inheritdoc
     */
    public function getRepositoryAttributes(): array
    {
        return [
            'battle_id' => $this->id,
        ];
    }
}
