<?php

namespace Ryp\Utils;

use Closure;
use Illuminate\Support\Facades\Cache;

class RypRequest
{
    use RypReturnJson;
    
    public function handle($request, Closure $next)
    {
        $this->cacheIdBox($request->request_id ?? '');
        return $next($request);
    }
    
    public function cacheIdBox($request_id)
    {
        if (empty($request_id)) {
            throw new RypException('request fail.', 100);
        }
        if (!Cache::get('request_id')) {
            $this->cacheArray($request_id);
        }
        $requestArray = \GuzzleHttp\json_decode(Cache::get('request_id'), true);
        if (in_array($request_id, $requestArray)) {
            throw new RypException('request fail.', 100);
        }
        array_push($requestArray, $request_id);
        $this->cacheArray($requestArray);
    }
    
    public function cacheArray($request_id, $timeOut = 60 * 24)
    {
        $data = is_array($request_id) ? \GuzzleHttp\json_encode($request_id)
            : \GuzzleHttp\json_encode([
                $request_id
            ]);
        Cache::put('request_id', $data, $timeOut);
    }
    
}