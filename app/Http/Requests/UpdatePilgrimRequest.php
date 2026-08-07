<?php

namespace App\Http\Requests;

use App\Models\Pilgrim;

class UpdatePilgrimRequest extends StorePilgrimRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Pilgrim $pilgrim */
        $pilgrim = $this->route('pilgrim');

        return $this->baseRules($pilgrim->id);
    }
}
