<?php

namespace Imamuseum\EmuClient;

class EmuController {

    protected $config;

    public function __construct($config)
    {
        $this->irns = $this->setIrns();
        $this->chunk = $config['chunk'];
        $this->export_path = $config['export_path'];
    }

    public function getSpecificObject($irn)
    {
        $key = array_search($irn, $this->irns);
        $file = 'export-' . floor(abs($key / $this->chunk)) . '.json';
        $export = json_decode(file_get_contents($this->export_path . "/$file"));
        return $export->data->$irn;
    }

    public function getAllObjectIDs()
    {
        return $this->irns;
    }

    public function setIrns()
    {
        return json_decode(file_get_contents($this->export_path . '/emu-irns.json'));
    }

    public function getExportFileCount()
    {
        $object_count = count($this->irns);
        $file_count = floor(abs($object_count / $this->chunk));
        return $file_count;
    }

}
