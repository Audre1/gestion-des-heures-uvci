<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Exporter en PDF
     */
    public function exportPDF(Request $request)
    {
        $title = $request->input('title');
        $headers = json_decode($request->input('headers'), true);
        $rows = json_decode($request->input('rows'), true);
        $filename = $request->input('filename');

        return $this->exportService->exportPDF(
            $title,
            $headers,
            $rows,
            $filename ?? null
        );
    }

    /**
     * Exporter en Excel
     */
    public function exportExcel(Request $request)
    {
        $title = $request->input('title');
        $headers = json_decode($request->input('headers'), true);
        $rows = json_decode($request->input('rows'), true);
        $filename = $request->input('filename');

        return $this->exportService->exportExcel(
            $title,
            $headers,
            $rows,
            $filename ?? null
        );
    }
}
