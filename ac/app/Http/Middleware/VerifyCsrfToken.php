<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'auth/login/step1',
        'auth/login/step2',
        'ropc/counting/pdf',
        'aro/counting/pdf',
		'payment-return-handle',
		'payment-verification',
		'payment-return-first-call', 
		'payment-gujrat-verification',
		'payment-return-handle-wb',
		'payment-verification-wb',
		'payment-return-handle-pd',
		'payment-verification-pd',
		'payment-return-handle-ke',
		'payment-verification-ke',
		'payment-return-handle-tm',
		'payment-verification-tm',
		'payment-return-handle-aa',
		'payment-verification-aa',
		'payment-return-handle-up',
		'payment-verification-up',
		'payment-return-handle-man',
		'payment-verification-man',
		'payment-return-handle-pun',
		'payment-verification-pun',
		'payment-return-handle-goa',
		'payment-verification-goa',
		'payment-return-handle-uk',
		'payment-verification-uk',
		'payment-return-handle-hp',
		'payment-verification-hp',
		'payment-return-handle-tri',
		'payment-verification-tri',
		'payment-return-handle-meg',
		'payment-verification-meg',
		'payment-return-handle-nag',
		'payment-verification-nag',
		'payment-return-handle-kar',
		'payment-verification-kar',
		'payment-return-handle-mp',
		'payment-verification-mp',
		'payment-return-handle-odi',
		'payment-verification-odi',
		'payment-scroll-recieved'
    ];
}
