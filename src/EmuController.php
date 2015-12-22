<?php

namespace Imamuseum\EmuClient;

class EmuController {

    protected $config;

    public function __construct($config)
    {
        $this->export_path = $config['export_path'];
        $this->chunk = $config['chunk'];
    }

    public function getSpecificObject($irn, $type = 'update')
    {
        $object_ids = $type == 'update' ? $this->getUpdatedObjectIDs() : $this->getAllObjectIDs();
        $key = array_search($irn, $object_ids);

        $file = 'export-' . floor(abs($key / $this->chunk)) . '.json';
        $export = json_decode(file_get_contents($this->export_path . "/$file"));
        return $export->data->$irn;
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
