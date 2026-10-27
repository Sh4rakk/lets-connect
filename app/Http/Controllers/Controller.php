<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

// authorizeResource() in de api controllers heeft deze base controller + trait nodig
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
}
