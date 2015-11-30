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

        $this->makeExportDirectory();
        $this->deleteJsonFiles();

        $session = new \IMuSession($config['host'], $config['port']);
        $session->connect();

        $this->catalogue = new \IMuModule('ecatalogue', $session);
        $terms = new \IMuTerms();
        $this->count = $this->catalogue->findTerms($terms);
        $this->fields = $config['fields'];

        $this->start = $config['start'];
        $this->chunk = $config['chunk'];

    }

    public function saveJsonFiles()
    {
        $this->catalogue->addFetchSet('exportFields', $this->fields);

        $i = $this->start;
        $count = 0;
        $e = $this->start + $this->chunk;

        while ($i <= $this->count) {
            $result = $this->catalogue->fetch('start', $i, 1, 'exportFields');
            // check if result has data
            if (isset($result->rows[0])) {
                // get irn from result
                $irn = $result->rows[0]['irn'];
                $irns[] = $irn;
                // add data to result
                $results['data'][$irn] = array(
                    $result->rows[0],
                );
            }
            $i++;

            if ($i == $e || $i == $this->count) {
                // var_dump("export-$count.json");
                file_put_contents($this->export_path . "/export-$count.json", json_encode($results));
                $results = array();
                $e = $e + $this->chunk;
                $count++;
            }
        }
        file_put_contents($this->export_path . "/emu-irns.json", json_encode($irns));
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
}
