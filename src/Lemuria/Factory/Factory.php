<?php

/*
 * This file is a part of the Lemuria project.
 *
 * Copyright (c) 2021-present Valithor Obsidion <valzargaming@gmail.com>
 */

namespace Lemuria\Factory;

use Lemuria\Lemuria;
use Lemuria\Http;
use Lemuria\Parts\Part;
use Lemuria\Repository\AbstractRepository;

/**
 * Exposes an interface to build part objects without the other requirements.
 */
class Factory
{
    /**
     * The Lemuria client.
     *
     * @var lemuria Client.
     */
    public $lemuria;
	
    /**
     * The HTTP client.
     *
     * @var Http Client.
     */
    protected $http;

    /**
     * Constructs a factory.
     *
     * @param Lemuria $lemuria The Lemuria client.
	 * @param Http    $http    The HTTP client.
     */
    public function __construct(Lemuria $lemuria, Http $http)
    {
        $this->lemuria = $lemuria;
		$this->http = $http;
    }

    /**
     * Creates an object.
     *
     * @param string $class   The class to build.
     * @param mixed  $data    Data to create the object.
     * @param bool   $created Whether the object is created (if part).
     *
     * @return Part|AbstractRepository The object.
     * @throws \Exception
     */
    public function create(string $class, $data = [], bool $created = false)
    {
        if (! is_array($data)) {
            $data = (array) $data;
        }

        if (strpos($class, 'Lemuria\\Parts') !== false) {
            $object = $this->part($class, $data, $created);
        } elseif (strpos($class, 'Lemuria\\Repository') !== false) {
            $object = $this->repository($class, $data/*, $this->lemuria->browser*/);
        } else {
            throw new \Exception('The class '.$class.' is not a Part or a Repository.');
        }

        return $object;
    }

    /**
     * Creates a part.
     *
     * @param string $class   The class to build.
     * @param array  $data    Data to create the object.
     * @param bool   $created Whether the object is created (if part).
     *
     * @return Part The part.
     */
    public function part(string $class, array $data = [], bool $created = false): Part
    {
        return new $class($this->lemuria, $data, $created);
    }

    /**
     * Creates a repository.
     *
     * @param string $class The class to build.
     * @param array  $data  Data to create the object.
     *
     * @return AbstractRepository The repository.
     */
    public function repository(string $class, array $data = [], $browser = null): AbstractRepository
    {
		if ($class == 'Player')
			return new $class($this->http, $this, $data, $browser);
        return new $class($this->http, $this, $data);
    }
}
