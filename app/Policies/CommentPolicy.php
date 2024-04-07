<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given post can be deleted by the user.
     *
     * @param User $user
     * @param  Comment  $post
     * @return bool
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->owner_id;
    }
}
