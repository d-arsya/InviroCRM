<?php

namespace App\Exceptions;

use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RouteMissing extends Exception
{
    use ApiResponder;

    public function __invoke(RouteNotFoundException $e, Request $request)
    {
        return $this->error('Check your route', 404, null);
    }
}
