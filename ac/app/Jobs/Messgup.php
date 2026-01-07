<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\SmsgatewayHelper;


class Messgup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_mobile;
    protected $message;
    public $timeout = 80;
    public $tries = 10;

    public function __construct($user_mobile, $message)
    {
        $this->user_mobile = $user_mobile;
        $this->message = $message;
    }

    public function handle()
    {
        // Send notification message via SMS
        SmsgatewayHelper::gupshup($this->user_mobile, $this->message);
    }
}