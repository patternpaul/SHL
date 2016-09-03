<?php

namespace App\Http\Controllers;

use App\Infrastructure\Aggregate\AggregateException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    protected function dispatchCommand($command, $request)
    {
        try {
            return $this->dispatch($command);
        } catch (AggregateException $exception) {
            throw new HttpResponseException(
                $this->buildFailedValidationResponse(
                    $request,
                    ["aggregate" => $exception->getMessage()]
                )
            );
        }
    }
}
