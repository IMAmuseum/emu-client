<?php

namespace Imamuseum\EmuClient;

require_once 'imu-api/IMu.php';
require_once \IMu::$lib . '/Session.php';
require_once \IMu::$lib . '/Module.php';
require_once \IMu::$lib . '/Terms.php';

class EmuExport {

    protected $config;

    public function __construct($config)
    {
        $this->export_path = $config['export_path'];

        $session = new \IMuSession($config['host'], $config['port']);
        $session->connect();

        $this->catalogue = new \IMuModule('ecatalogue', $session);
        $terms = new \IMuTerms();
        $this->count = $this->catalogue->findTerms($terms);
        $this->fields = $config['fields'];
        $this->catalogue->addFetchSet('exportFields', $this->fields);

        $this->start = $config['start'];
        $this->chunk = $config['chunk'];
    }

    public function saveJsonFile($start, $count)
    {
        $results = $this->catalogue->fetch('start', $start, $this->chunk, 'exportFields');
        foreach ($results->rows as $row) {
            $irn = $row['irn'];
            $irns[] = $irn;
            $data['data'][$irn] = [$row];
        }
        file_put_contents($this->export_path . "/export-$count.json", json_encode($data));
        $this->updateIrnFile($irns, $count);
    }

    public function updateIrnFile($irns, $count)
    {
        if ($count == 0) {
            $new_irns = $irns;
        }

        if ($count != 0) {
            $file_irns = json_decode(file_get_contents($this->export_path . "/emu-irns.json"));
            $new_irns = array_merge($file_irns, $irns);
        }

        file_put_contents($this->export_path . "/emu-irns.json", json_encode($new_irns));
    }

    public function deleteJsonFiles()
    {
        $files = glob($this->export_path . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function makeExportDirectory()
    {
        if (!file_exists($this->export_path)) {
            mkdir($this->export_path, 0755, true);
        }
    }

    public function getObjectCount()
    {
        return $this->count;
    }
}
