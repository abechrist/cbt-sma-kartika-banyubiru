<?php

namespace App\Http\Controllers\Api\ImportExport;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DapodikController extends Controller
{
    /**
     * Download Dapodik template.
     */
    public function template(Request $request): JsonResponse
    {
        // TODO: Implement template download

        return $this->successResponse(null, 'Template ready');
    }

    /**
     * Import from Dapodik.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        // TODO: Implement Dapodik import logic

        return $this->successResponse(null, 'Data imported from Dapodik');
    }

    /**
     * Export to Dapodik format.
     */
    public function export(Request $request): JsonResponse
    {
        // TODO: Implement Dapodik export logic

        return $this->successResponse(null, 'Export ready');
    }
}
