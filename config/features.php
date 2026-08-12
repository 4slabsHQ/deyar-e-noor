<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Phased Module Rollout
    |--------------------------------------------------------------------------
    |
    | Toggle operational modules independently for staged production releases.
    | Routes stay registered but return 404 when disabled. Sidebar links
    | are hidden when a feature is off.
    |
    | Phase 1 (UAT): hajj_registration=false, flights=false
    | Phase 2: hajj_registration=true
    | Phase 3: flights=true
    |
    */

    'hajj_registration' => (bool) env('FEATURE_HAJJ_REGISTRATION', true),

    'flights' => (bool) env('FEATURE_FLIGHTS', true),

];
