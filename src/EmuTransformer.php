<?php

namespace Imamuseum\EmuClient;

use Exception;

class EmuTransformer
{
    protected $config;

    public function __construct($config)
    {
        $this->field_transform = $config['field_transform'];
        $this->field_addition = $config['field_addition'];
        $this->field_transform_class = new $config['field_transform_class'];
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