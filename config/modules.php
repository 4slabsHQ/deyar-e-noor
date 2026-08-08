<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Legacy Travel ERP Modules
    |--------------------------------------------------------------------------
    |
    | When false, out-of-scope demo modules are hidden from the sidebar:
    | Organization, CRM, Parties, and travel-specific master data (airlines,
    | hotels, etc.). Routes remain registered so they can be re-enabled later.
    |
    */

    'show_legacy_travel_erp' => (bool) env('SHOW_LEGACY_TRAVEL_ERP', false),

];
