<?php
declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdatePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ChangeUserPasswordController extends Controller
{

    /**
     * @param UserUpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function __invoke(UserUpdatePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();

        if ( !Hash::check($request->get('current_password'), $user->password)) {
            return response()->json(['message' => 'not found'], Response::HTTP_NOT_FOUND);
        }

        $user->password = Hash::make($request->get('new_password'));
        $user->save();

        return response()->json(['message' => 'password successfully changed '], Response::HTTP_OK);
    }
}
