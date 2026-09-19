<?php

namespace App\Http\Controllers\Api\Rpp;

use App\Http\Controllers\Api\Controller;
use App\Models\Rpp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RppController extends Controller
{
    /**
     * Display a listing of RPP.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Rpp::with(['subject', 'materials', 'assessments', 'createdBy']);

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $rpps = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($rpps);
    }

    /**
     * Store a newly created RPP.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'grade_level' => 'required|string',
            'semester' => 'required|string',
            'duration' => 'required|string',
            'objectives' => 'required|array',
            'materials' => 'sometimes|array',
            'methods' => 'sometimes|array',
            'assessment' => 'sometimes|array',
        ]);

        $rpp = Rpp::create($request->all());

        // Create materials if provided
        if ($request->has('materials')) {
            foreach ($request->materials as $material) {
                $rpp->materials()->create($material);
            }
        }

        // Create assessments if provided
        if ($request->has('assessment')) {
            foreach ($request->assessment as $assessment) {
                $rpp->assessments()->create($assessment);
            }
        }

        return $this->createdResponse($rpp->load(['materials', 'assessments']), 'RPP created successfully');
    }

    /**
     * Display the specified RPP.
     */
    public function show(Rpp $rpp): JsonResponse
    {
        $rpp->load(['subject', 'materials', 'assessments', 'createdBy', 'courses', 'exams']);

        return $this->successResponse($rpp);
    }

    /**
     * Update the specified RPP.
     */
    public function update(Request $request, Rpp $rpp): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'subject_id' => 'sometimes|exists:subjects,id',
            'grade_level' => 'sometimes|string',
            'semester' => 'sometimes|string',
            'duration' => 'sometimes|string',
            'objectives' => 'sometimes|array',
            'materials' => 'sometimes|array',
            'methods' => 'sometimes|array',
            'assessment' => 'sometimes|array',
        ]);

        $rpp->update($request->except(['materials', 'assessment']));

        // Update materials
        if ($request->has('materials')) {
            $rpp->materials()->delete();
            foreach ($request->materials as $material) {
                $rpp->materials()->create($material);
            }
        }

        // Update assessments
        if ($request->has('assessment')) {
            $rpp->assessments()->delete();
            foreach ($request->assessment as $assessment) {
                $rpp->assessments()->create($assessment);
            }
        }

        return $this->successResponse($rpp->load(['materials', 'assessments']), 'RPP updated successfully');
    }

    /**
     * Remove the specified RPP.
     */
    public function destroy(Rpp $rpp): JsonResponse
    {
        $rpp->delete();

        return $this->noContentResponse('RPP deleted successfully');
    }

    /**
     * Get integration status of RPP.
     */
    public function getIntegrationStatus(Rpp $rpp): JsonResponse
    {
        return $this->successResponse([
            'rpp_id' => $rpp->id,
            'has_courses' => $rpp->courses()->count() > 0,
            'has_exams' => $rpp->exams()->count() > 0,
            'courses_count' => $rpp->courses()->count(),
            'exams_count' => $rpp->exams()->count(),
            'integration_status' => $rpp->courses()->count() > 0 && $rpp->exams()->count() > 0 ? 'complete' : 'partial',
        ]);
    }
}
