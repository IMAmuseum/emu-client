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
        $this->emu->saveJsonFiles();
    }
}
