<?php

namespace App\CommandBus\Middlewares;

use Illuminate\Support\Facades\DB;
use League\Tactician\Middleware;

class TransactionMiddleware implements Middleware
{
    private int $attempts;

    /**
     * @param int $attempts
     */
    public function __construct (int $attempts = 2)
    {
        $this->attempts = $attempts;
    }

    /**
     * @inheritDoc
     */
    public function execute ($command, callable $next)
    {
        return DB::transaction(function () use ($command, $next)
        {
            return $next($command);
        }, $this->attempts);
    }
}