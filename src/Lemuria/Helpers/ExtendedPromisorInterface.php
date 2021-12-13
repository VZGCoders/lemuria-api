<?php

/*
 * This file is a part of the Lemuria project.
 *
 * Copyright (c) 2021-present Valithor Obsidion <valzargaming@gmail.com>
 */

namespace Lemuria\Helpers;

use React\Promise\ExtendedPromiseInterface;

/**
 * Expands on the react/promise PromisorInterface
 * by returning an extended promise.
 */
interface ExtendedPromisorInterface
{
    /**
     * Returns the promise of the deferred.
     *
     * @return ExtendedPromiseInterface
     */
    public function promise(): ExtendedPromiseInterface;
}
