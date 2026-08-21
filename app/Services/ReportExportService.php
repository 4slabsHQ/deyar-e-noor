<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * @param  list<string>  $headings
     * @param  list<list<string|int|null>>  $rows
     */
    public function toPdf(string $title, array $headings, array $rows, string $filename): Response
    {
        $pdf = Pdf::loadView('admin.reports.export-pdf', [
            'title' => $title,
            'headings' => $headings,
            'rows' => $rows,
            'generatedAt' => now()->format('d M Y H:i'),
        ])->setPaper('a4', count($headings) > 6 ? 'landscape' : 'portrait');

        return $pdf->download($filename);
    }

    /**
     * @param  list<string>  $headings
     * @param  list<list<string|int|null>>  $rows
     */
    public function toExcel(string $title, array $headings, array $rows, string $filename): Response
    {
        $content = view('admin.reports.export-excel', [
            'title' => $title,
            'headings' => $headings,
            'rows' => $rows,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * @param  list<string>  $headings
     * @param  list<list<string|int|null>>  $rows
     */
    public function toCsv(array $headings, array $rows, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, $headings);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
