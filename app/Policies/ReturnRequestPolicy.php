<?php

namespace App\Policies;

use App\Models\ReturnRequest;
use App\Models\User;

class ReturnRequestPolicy
{
    public function view(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->id === $returnRequest->user_id;
    }
}
