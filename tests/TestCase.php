<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{

    protected mixed $user;

    public function actingAsSubscribedUser(User $user): void
    {
        $this->user = Mockery::mock($user)->makePartial();
        $this->user->shouldReceive('subscribed')->andReturn(true);
        $this->actingAs($this->user);
    }
}
