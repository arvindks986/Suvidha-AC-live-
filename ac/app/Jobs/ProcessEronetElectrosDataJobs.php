<?php

namespace App\Jobs;

use App\models\Admin\polling_station\PollingStationModel;
use App\Services\GetGenderWiseElectorsCountService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessEronetElectrosDataJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pollingStation;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PollingStationModel $pollingStation)
    {
        $this->pollingStation = $pollingStation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        GetGenderWiseElectorsCountService::getDataForPSFromEROnet($this->pollingStation);
        Log::info('Job hits with value => '.$this->pollingStation->CCODE);
    }
}
