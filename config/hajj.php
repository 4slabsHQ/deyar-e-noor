<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Active Hajj Year
    |--------------------------------------------------------------------------
    |
    | Used when no Hajj season is marked active in the database. Set this on
    | production when first deploying seasons (e.g. HAJJ_ACTIVE_YEAR=2027).
    |
    */

    'default_active_year' => env('HAJJ_ACTIVE_YEAR'),

];
