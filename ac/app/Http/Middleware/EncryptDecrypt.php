<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\EncryptionService;

class EncryptDecrypt
{
    /**
     * Fields that should be decrypted
     */
    protected $decryptFields = [
        'mobile',
        'otp',
        'password',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $data = $request->all();

        foreach ($this->decryptFields as $field) {

            if ($request->filled($field)) {

                try {
                    $decrypted = EncryptionService::decrypt(
                        $request->input($field)
                    );

                    // Only replace if decryption succeeded
                    if ($decrypted !== null) {
                        $data[$field] = $decrypted;
                    }

                } catch (\Exception $e) {
                    // Fail silently or log if required
                }
            }
        }

        // Replace request data with decrypted values
        $request->merge($data);

        return $next($request);
    }
}
