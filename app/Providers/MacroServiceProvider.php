<?php

namespace App\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register ()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot () : void
    {
        ResponseFactory::macro('jsonWrap',
            function ($data = [], int $status = Response::HTTP_OK, string $message = 'Successful', array $headers = [], int $options = 0)
            {
                $wrapData = ['data' => $data, 'message' => $message];
                return $this->json($wrapData, $status, $headers, $options);
            });
    }
}
