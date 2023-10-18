<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 27.10.2018
 * Time: 16:21
 */

namespace Cintas\Http\Logger;


use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Spatie\HttpLogger\LogWriter;

class ApiLogger implements LogWriter
{
    public function logRequest(Request $request)
    {
        $method = strtoupper($request->getMethod());

        $uri = $request->getPathInfo();

        $ip = $request->ip();

        $bodyAsJson = json_encode($request->except(config('http-logger.except')));

        $files = array_map(function (UploadedFile $file) {
            return $file->getClientOriginalName();
        }, iterator_to_array($request->files));

        $message = "{$ip} - {$method} {$uri} - Body: {$bodyAsJson} - Files: " . implode(', ', $files);

        Log::channel('api')->info($message);
    }
}