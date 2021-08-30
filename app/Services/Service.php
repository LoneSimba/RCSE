<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class Service
{
    /**
     * @param \Closure $callback
     * @param int $attempts
     * @return mixed|null
     */
    protected function transaction(\Closure $callback, int $attempts = 1)
    {
        try {
            return DB::transaction($callback, $attempts);
        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }
    }

}
