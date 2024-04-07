<?php
declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateNameRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ChangeUserNameController extends Controller
{

    /**
     * @param UserUpdateNameRequest $request
     * @return JsonResponse
     */
    public function __invoke(UserUpdateNameRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        auth()->user()->update($validatedData);

        return response()->json(['message' => 'name successfully changed'], Response::HTTP_OK);
    }
}
