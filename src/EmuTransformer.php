<?php

namespace Imamuseum\PictionClient;

use Exception;

class PictionTransformer
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function transform($data, $images)
    {

    }

    // transform an individual object
    public function item($data)
    {

    }

    // transform a collection of objects
    public function collection($data)
    {

    }

    // transform field data
    public function transformFields($data)
    {

    }

    // add fields to data
    public function addFields($data)
    {

    }
}