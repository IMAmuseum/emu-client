<?php

namespace Imamuseum\EmuClient\Console\Commands;

use Illuminate\Console\Command;
use Imamuseum\EmuClient\EmuExport;

class EmuExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emu:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export JSON from Emu Client.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->emu = new EmuExport(config('emu-client'));
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->emu->makeExportDirectory();
        $this->emu->deleteJsonFiles();

        $count = 0;
        $chunk = config('emu-client.chunk');
        $file_count = (int)floor($this->emu->getObjectCount() / $chunk);
        $this->output->progressStart($file_count);
        while ($count <= $file_count) {
            $start = ($count * $chunk) + $chunk;
            $this->emu->saveJsonFile($start, $count);
            $this->output->progressAdvance();
            $count++;
        }
        $this->output->progressFinish();
    }
}
