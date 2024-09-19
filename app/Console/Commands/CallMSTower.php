<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CallMSTower extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:call-ms-tower';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Call MS Tower's API to get pole stress data";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $commands = [
            'UiRobot.exe -file D:/ungsuat/MSTower.1.0.10.nupkg -input "{\"excelPath\":\"D:\\\\ungsuat\\\\ung_suat.xlsx\"}"'
        ];

        $fullCommand = implode(' && ', $commands);
        $process = Process::fromShellCommandline($fullCommand);
        $process->setTimeout(3600);
        try{
            echo "Start calling MS Tower API\n";
            $process->run();
            if (!$process->isSuccessful()) {
                echo "Error: ".$process->getErrorOutput();
                throw new ProcessFailedException($process);
            }

            $this->info("MS Tower API has been called successfully");
            $this->info("Output: ".$process->getOutput());
        } catch (ProcessFailedException $exception){
            $this->error("Error: ".$exception->getMessage());
        }
    }
}
