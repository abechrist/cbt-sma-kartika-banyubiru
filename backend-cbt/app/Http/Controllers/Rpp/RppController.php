<?php

namespace App\Http\Controllers\Rpp;

use App\Http\Controllers\Controller;
use App\Models\Rpp;
use App\Models\Subject;
use App\Services\RppIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RppController extends Controller
{
    public function __construct(
        private RppIntegrationService $integrationService
    ) {}

    public function index(Request $request)
    {
        $query = Rpp::with(['subject', 'classGroup', 'teacher']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                    ->orWhere('academic_year', 'like', "%{$search}%")
                    ->orWhere('semester', 'like', "%{$search}%")
                    ->orWhereHas('subject', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('classGroup', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rpps = $query->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('rpps.index', compact('rpps', 'subjects'));
    }

    public function create()
    {
        return view('rpps.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rpp_data' => 'required|array',
            'rpp_data.rpp_metadata' => 'required|array',
            'rpp_data.rpp_metadata.mata_pelajaran' => 'required|string',
            'rpp_data.rpp_metadata.kelas' => 'required|string',
            'rpp_data.rpp_metadata.topik_utama' => 'required|string',
            'rpp_data.lms_integration' => 'required|array',
            'rpp_data.cbt_integration' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $rpp = $this->integrationService->importRpp($request->rpp_data);

            return response()->json([
                'success' => true,
                'message' => 'RPP berhasil diimpor dan diintegrasikan',
                'data' => [
                    'rpp_id' => $rpp->id,
                    'subject' => $rpp->subject->name,
                    'topic' => $rpp->topic,
                    'status' => $rpp->status,
                    'materials_count' => $rpp->materials()->count(),
                    'assessments_count' => $rpp->assessments()->count(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor RPP: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        $rpp = Rpp::with(['subject', 'classGroup', 'teacher', 'materials', 'assessments'])->findOrFail($id);
        $activeTab = request()->query('tab', 'overview');

        return view('rpps.show', compact('rpp', 'activeTab'));
    }

    public function edit(string $id)
    {
        $rpp = Rpp::with(['materials', 'assessments'])->findOrFail($id);

        return view('rpps.edit', compact('rpp'));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $rpp = Rpp::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'topic' => 'sometimes|string|max:255',
            'time_allocation' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:draft,published,integrated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $rpp->update($request->only(['topic', 'time_allocation', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'RPP berhasil diperbarui',
            'data' => $rpp,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $rpp = Rpp::findOrFail($id);
        $rpp->delete();

        return response()->json([
            'success' => true,
            'message' => 'RPP berhasil dihapus',
        ]);
    }

    public function getIntegrationStatus(string $id): JsonResponse
    {
        $rpp = Rpp::with(['materials.learningMaterial', 'assessments.exam'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'rpp_id' => $rpp->id,
                'topic' => $rpp->topic,
                'status' => $rpp->status,
                'lms_integration' => [
                    'course_id' => $rpp->materials->first()?->learningMaterial?->course_id,
                    'materials_count' => $rpp->materials->count(),
                    'materials' => $rpp->materials->map(fn ($m) => [
                        'pertemuan' => $m->pertemuan_ke,
                        'title' => $m->judul_topik,
                        'material_id' => $m->learning_material_id,
                    ]),
                ],
                'cbt_integration' => [
                    'exam_id' => $rpp->assessments->first()?->exam_id,
                    'assessments_count' => $rpp->assessments->count(),
                    'tps' => $rpp->assessments->map(fn ($a) => [
                        'tp_id' => $a->tp_id,
                        'deskripsi' => $a->deskripsi_tp,
                        'tingkat_kesulitan' => $a->tingkat_kesulitan,
                    ]),
                ],
            ],
        ]);
    }
}
