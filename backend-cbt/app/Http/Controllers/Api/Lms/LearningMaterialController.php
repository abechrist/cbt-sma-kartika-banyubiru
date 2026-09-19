<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Api\Controller;
use App\Models\Course;
use App\Models\LearningMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LearningMaterialController extends Controller
{
    /**
     * Display a listing of materials for a course.
     */
    public function index(Request $request, Course $course): JsonResponse
    {
        $materials = $course->materials()
            ->with(['progress' => function ($q) {
                $q->where('user_id', auth()->id());
            }])
            ->orderBy('order')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($materials);
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:text,video,document,link',
            'order' => 'sometimes|integer',
            'file_path' => 'sometimes|string',
            'external_url' => 'sometimes|url',
        ]);

        $material = $course->materials()->create($request->all());

        return $this->createdResponse($material, 'Material created successfully');
    }

    /**
     * Display the specified material.
     */
    public function show(Course $course, LearningMaterial $material): JsonResponse
    {
        $material->load(['progress' => function ($q) {
            $q->where('user_id', auth()->id());
        }]);

        return $this->successResponse($material);
    }

    /**
     * Update the specified material.
     */
    public function update(Request $request, Course $course, LearningMaterial $material): JsonResponse
    {
        $material->update($request->all());

        return $this->successResponse($material, 'Material updated successfully');
    }

    /**
     * Remove the specified material.
     */
    public function destroy(Course $course, LearningMaterial $material): JsonResponse
    {
        $material->delete();

        return $this->noContentResponse('Material deleted successfully');
    }
}
