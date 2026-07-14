<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    /**
     * Exporter les données en PDF avec le logo UVCI
     */
    public function exportPDF($title, $headers, $rows, $filename = null)
    {
        $filename = $filename ?? 'export-' . date('Y-m-d-His') . '.pdf';
        
        $logoPath = public_path('images/logo-simple.png');
        $logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
        
        $data = [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'logo' => $logoData,
            'date' => now()->format('d/m/Y H:i'),
        ];
        
        $pdf = Pdf::loadView('exports.pdf-template', $data);
        
        // Configuration DomPDF
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        
        return $pdf->download($filename);
    }
    
    /**
     * Exporter les données en Excel avec formatage
     */
    public function exportExcel($title, $headers, $rows, $filename = null)
    {
        $filename = $filename ?? 'export-' . date('Y-m-d-His') . '.xlsx';
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Titre du document
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:' . $this->getColumnLetter(count($headers)) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00A54E'); // UVCI Green
        
        // Date d'export
        $sheet->setCellValue('A2', 'Exporté le: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A2:' . $this->getColumnLetter(count($headers)) . '2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF666666'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // En-têtes
        $row = 4;
        foreach ($headers as $col => $header) {
            $cell = $this->getColumnLetter($col + 1) . $row;
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00A54E');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
        }
        
        // Données
        $row = 5;
        foreach ($rows as $rowData) {
            foreach ($rowData as $col => $value) {
                $cell = $this->getColumnLetter($col + 1) . $row;
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('DDDDDD');
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }
            $row++;
        }
        
        // Ajuster la largeur des colonnes
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimension($this->getColumnLetter($col))->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        
        // Sauvegarder temporairement et télécharger
        $tempFile = storage_path('app/temp/' . $filename);
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
    
    /**
     * Convertir un numéro de colonne en lettre (1 = A, 2 = B, etc.)
     */
    private function getColumnLetter($columnNumber)
    {
        if ($columnNumber <= 0) {
            return '';
        }
        
        $letter = '';
        while ($columnNumber > 0) {
            $remainder = ($columnNumber - 1) % 26;
            $letter = chr(65 + $remainder) . $letter;
            $columnNumber = floor(($columnNumber - 1) / 26);
        }
        
        return $letter;
    }
}
