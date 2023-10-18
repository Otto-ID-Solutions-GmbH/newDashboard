<?php

namespace Cintas\Http\Controllers\Shared;

use Cintas\Exceptions\ExternalDeviceException;
use Cintas\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ApiLoggerController extends Controller
{

    public function __invoke(Request $request, $severity = 'info')
    {
        if ($severity == 'critical' || $severity == 'error') {
            report(new ExternalDeviceException($request->log_message, 0, null, $severity));
        }

        $message = $request->log_message . "\n" . json_encode($request->all());
        Log::channel('api_error')->{$severity}($message);
    }
}
