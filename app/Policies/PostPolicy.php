<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the given post can be updated by the user.
     *
     * @param User $user
     * @param  Post  $post
     * @return bool
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->owner_id;
    }

    /**
     * Determine if the given post can be deleted by the user.
     *
     * @param User $user
     * @param  Post  $post
     * @return bool
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->owner_id;
    }
}
