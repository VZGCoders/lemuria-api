<?php

/*
 * This file is a part of the Lemuria project.
 *
 * Copyright (c) 2021-present Valithor Obsidion <valzargaming@gmail.com>
 */

namespace Lemuria\Exceptions;

/**
 * Thrown when a request that was executed from a part failed.
 *
 * @see \Lemuria\Parts\Part::save() Can be thrown when being saved.
 * @see \Lemuria\Parts\Part::delete() Can be thrown when being deleted.
 */
class PartRequestFailedException extends \Exception
{
}
