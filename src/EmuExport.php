<?php

namespace Imamuseum\EmuClient;

require_once 'imu-api/IMu.php';
require_once \IMu::$lib . '/Session.php';
require_once \IMu::$lib . '/Module.php';
require_once \IMu::$lib . '/Terms.php';

use Carbon\Carbon;
use Imamuseum\EmuClient\EmuTransformer;

class EmuExport {

    protected $config;
    protected $type;

    public function __construct($config, $type)
    {
        $this->update_since = $this->setUpdateTimestamp($config['update_since']);
        $this->export_path = $config['export_path'];

        $session = new \IMuSession($config['host'], $config['port']);
        $session->connect();

        $this->catalogue = new \IMuModule('ecatalogue', $session);
        $terms = new \IMuTerms();
        if ($type == 'update') {
            $terms->add('AdmDateModified', $this->update_since, '>');
        }
        $this->count = $this->catalogue->findTerms($terms);
        $this->fields = $config['fields'];
        $this->catalogue->addFetchSet('exportFields', $this->fields);

        $this->start = $config['start'];
        $this->chunk = $config['chunk'];
        $this->transform = $config['transform_data'];
    }

    public function doEmuExport()
    {
        $count = 0;
        $start = $this->start;
        $file_count = (int)floor($this->getObjectCount() / $this->chunk);
        while ($count <= $file_count) {
            $this->saveJsonFile($start, $count);
            $start += $this->chunk;
            $count++;
        }
    }

    public function saveJsonFile($start, $count)
    {
        $results = $this->getEmuResults($start);

        foreach ($results->rows as $row) {
                $irn = $row['irn'];
                $irns[] = $irn;
            if (json_encode($row)) {
                $data['data'][$irn] = $row;
            } else {
                $data['data'][$irn] = 'error';
                $error = $this->checkJsonEncodeError();
                $data['data'][$irn] = ['error' => $error];
            }
        }
        file_put_contents($this->export_path . "/export-$count.json", json_encode($data));
        $this->updateIrnFile($irns, $count);
    }

    public function getEmuResults($start)
    {
        $results = $this->catalogue->fetch('start', $start, $this->chunk, 'exportFields');
        return $results;
    }

    public function updateIrnFile($irns, $count)
    {
        if ($count == 0) {
            $new_irns = $irns;
        }

        if ($count != 0) {
            $file_irns = json_decode(file_get_contents($this->export_path . "/emu-update-irns.json"));
            $new_irns = array_merge($file_irns, $irns);
        }

        file_put_contents($this->export_path . "/emu-update-irns.json", json_encode($new_irns));
    }

    public function makeAllIrnFile()
    {
        $irns = null;
        $catalogue = new \IMuModule('ecatalogue', $this->session);
        $catalogue_terms = new \IMuTerms();
        $catalogue->findTerms($catalogue_terms);
        $results = $catalogue->fetch('start', 0, -1, ['irn']);
        foreach ($results->rows as $row) {
            $irn = $row['irn'];
            $irns[] = $irn;
        }
        file_put_contents(__DIR__ . "/../data/emu-all-irns.json", json_encode($irns));
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
        return $this->count - $this->start;
    }

    public function setUpdateTimestamp($since)
    {
        return Carbon::now()->subDays($since)->format('m/d/Y');
    }

    public function getUpdateTimestamp()
    {
        return $this->update_since;
    }

    public function checkJsonEncodeError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = "No errors";
                break;
            case JSON_ERROR_DEPTH:
                $error = "Maximum stack depth exceeded";
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = "Underflow or the modes mismatch";
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = "Unexpected control character found";
                break;
            case JSON_ERROR_SYNTAX:
                $error = "Syntax error, malformed JSON";
                break;
            case JSON_ERROR_UTF8:
                $error = "Malformed UTF-8 characters, possibly incorrectly encoded";
                break;
            default:
                $error = "Unknown error";
            break;
        }
        return $error;
    }
}
