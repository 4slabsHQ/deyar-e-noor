<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesGuardedDeletion;

abstract class Controller
{
    use HandlesGuardedDeletion;
}
