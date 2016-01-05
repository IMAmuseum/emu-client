<?php

namespace Imamuseum\EmuClient;

use Imamuseum\EmuClient\EmuTransformer;

class EmuController {

    protected $config;

    public function __construct($config)
    {
        $this->transform = $config['transform_data'];
        if($this->transform ) $this->transformer = new EmuTransformer($config);
        $this->export_path = $config['export_path'];
        $this->chunk = $config['chunk'];
    }

    public function getSpecificObject($irn, $type='update', $transform=false)
    {
        $object_ids = ($type == 'update') ? $this->getUpdatedObjectIDs() : $this->getAllObjectIDs();
        $key = array_search($irn, $object_ids);

        $file = 'export-' . floor(abs($key / $this->chunk)) . '.json';
        $data = file_get_contents($this->export_path . "/$file");
        $objects = json_decode($data);
        $object = $transform ? $this->transformer->item($objects->data->$irn) : $objects->data->$irn;
        return json_encode($object);
    }

    public function getAllObjectIDs()
    {
        return $this->setAllIrns();
    }

    public function getUpdatedObjectIDs()
    {
        return $this->setUpdateIrns();
    }

    public function setAllIrns()
    {
        return json_decode(file_get_contents($this->export_path . '/emu-all-irns.json'));
    }

    public function setUpdateIrns()
    {
        return json_decode(file_get_contents($this->export_path . '/emu-update-irns.json'));
    }

    public function getImportFileCount()
    {
        $object_count = count($this->irns);
        $file_count = floor(abs($object_count / $this->chunk));
        return $file_count;
    }

}
