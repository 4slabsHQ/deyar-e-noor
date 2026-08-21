<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

trait HandlesGuardedDeletion
{
    protected function deleteOrBack(Model $model, string $redirectRoute, string $successMessage): RedirectResponse
    {
        if (method_exists($model, 'deletionBlockedMessage') && ($message = $model->deletionBlockedMessage())) {
            return back()->with('error', $message);
        }

        $model->delete();

        return redirect()->route($redirectRoute)->with('success', $successMessage);
    }
}
