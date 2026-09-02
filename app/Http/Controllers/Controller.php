<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AssignsActiveHajjYear;
use App\Http\Controllers\Concerns\HandlesGuardedDeletion;

abstract class Controller
{
    use AssignsActiveHajjYear;
    use HandlesGuardedDeletion;
}
