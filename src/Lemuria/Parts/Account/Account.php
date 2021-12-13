<?php

/*
 * This file is a part of the Lemuria project.
 *
 * Copyright (c) 2021-present Valithor Obsidion <valzargaming@gmail.com>
 */

namespace Lemuria\Parts\Account;

use Lemuria\Endpoint;
use Lemuria\Parts\Part;

/**
 * An Account is a collection of ids and references associated with a user.
 *

 * @property int    $id            The unique identifier of the Account.
 * @property int    $discord_id    Discord user id.

 */
class Account extends Part
{

    /**
     * @inheritdoc
     */
    protected static $fillable = ['id', 'discord_id'];

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
            'discord_id' => $this->discord_id,
        ];
    }

    /**
     * Returns a timestamp for when a Account's account was created.
     *
     * @return float
     */
    public function createdTimestamp()
    {
        return \Lemuria\getSnowflakeTimestamp($this->id);
    }
	

    /**
     * @inheritdoc
     */
    public function getRepositoryAttributes(): array
    {
        return [
            'account_id' => $this->id,
        ];
    }
	
	/*
	* Help documentation.
	*
	* @return string
	*/
	public function help(): string
	{
		return '';
	}
	
    /**
     * Returns a formatted mention.
     *
     * @return string A formatted mention.
     */
    public function __toString()
    {
        return "<@{$this->discord_id}>";
    }
}
