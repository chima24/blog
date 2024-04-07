<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CategoryCreateRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriesController extends Controller
{
    public function store(CategoryCreateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        auth()->user()->categories()->create($validatedData);

        return response()->json([], Response::HTTP_CREATED);
    }
}
