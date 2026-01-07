<?php

namespace App\Jobs;

use App\models\Admin\ElectorModel;
use App\Services\GetGenderWiseElectorsCountService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEronetACElectrosDataJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $electorModel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ElectorModel $electorModel)
    {
        $this->electorModel = $electorModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        GetGenderWiseElectorsCountService::getDataForACFromEROnet($this->electorModel);
    }
}
