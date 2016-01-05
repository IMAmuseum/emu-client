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
    protected $signature = 'emu:export
                            {--type=update : initial or update}';

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
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sync_type = $this->option('type');
        $emu = new EmuExport(config('emu-client'), $sync_type);
        $emu->makeExportDirectory();
        $emu->deleteJsonFiles();

        $count = 0;
        $chunk = config('emu-client.chunk');
        $start = config('emu-client.start');
        $file_count = (int)floor($emu->getObjectCount() / $chunk) + 1;
        $this->output->progressStart($file_count);
        while ($count <= $file_count) {
            $emu->saveJsonFile($start, $count);
            $start += $chunk;
            $this->output->progressAdvance();
            $count++;
        }
        $this->output->progressFinish();
    }
}
