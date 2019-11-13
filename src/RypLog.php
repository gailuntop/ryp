<?php

namespace Ryp\Utils;

use Illuminate\Support\Facades\Log;

class RypLog extends Log
{
	//
    public static function info($e)
    {
        Log::info($e);
        RypHandler::sendSysLogRequest(json_encode($e), 'text');
    }
}