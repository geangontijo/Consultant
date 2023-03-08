<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Panther\Client;
use Symfony\Component\Process\Process;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('whatsapp:run', function () {
    $process = new Process(['node', 'Whatsapp.js'], base_path('app/Broadcasting'));
    $process->setTimeout(null);
    $process->start();
    while ($process->isRunning()) {
        $incrementalOutput = $process->getIncrementalOutput();
        $this->info($incrementalOutput);
    }
})->purpose('Start the whatsapp bot');

Artisan::command('whatsapp:kimberle', function () {
//    $process = new Process(['node', 'Whatsapp.js'], base_path('app/Broadcasting'));
//    $process->setTimeout(null);
//    $process->start();

    $i = 0;
    while ($i < 100) {
        $i++;
        \Illuminate\Support\Facades\Queue::push('whatsapp.send.message', [
            'to' => '3788443845',
            'message' => 'coe macacao'
        ], 'whatsapp');
    }
});
