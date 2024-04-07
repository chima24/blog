<?php
declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateSurnameRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ChangeUserSurnameController extends Controller
{

    /**
     * @param UserUpdateSurnameRequest $request
     * @return JsonResponse
     */
    public function __invoke(UserUpdateSurnameRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        auth()->user()->update($validatedData);

        return response()->json(['message' => 'surname successfully changed'], Response::HTTP_OK);
    }
}
