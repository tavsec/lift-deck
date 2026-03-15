<?php

namespace App\Features;

use App\Models\User;

class Loyalty
{
    public function resolve(User $scope): bool
    {
        return false;
    }
}
