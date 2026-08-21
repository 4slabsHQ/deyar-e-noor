<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GuardsDeletionWhenReferenced
{
    /**
     * @return array<string, string> Eloquent relation method => linked record label
     */
    public function referencedByRelations(): array
    {
        return [];
    }

    /**
     * @return array<int, callable(Model&static): ?string>
     */
    public function referencedByChecks(): array
    {
        return [];
    }

    public function deletionResourceLabel(): string
    {
        return Str::headline(class_basename($this))->lower()->toString();
    }

    public function deletionBlockedMessage(): ?string
    {
        foreach ($this->referencedByRelations() as $relation => $label) {
            if (! method_exists($this, $relation)) {
                continue;
            }

            $count = $this->{$relation}()->count();

            if ($count > 0) {
                return sprintf(
                    'Cannot delete this %s because it is linked to %d %s.',
                    $this->deletionResourceLabel(),
                    $count,
                    $label,
                );
            }
        }

        foreach ($this->referencedByChecks() as $check) {
            if ($message = $check($this)) {
                return $message;
            }
        }

        return null;
    }
}
