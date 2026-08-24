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
     * @param  list<string>  $columnKeys
     */
    public function toPdf(string $title, array $headings, array $rows, string $filename, array $columnKeys = []): Response
    {
        $pdf = Pdf::loadView('admin.reports.export-pdf', [
            'title' => $title,
            'headings' => $headings,
            'rows' => $this->preparePdfRows($rows, $columnKeys),
            'columnKeys' => $columnKeys,
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
            'title' => $this->excelSafeText($title),
            'headings' => $headings,
            'rows' => $this->normalizeExportRows($rows),
        ])->render();

        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>'
            .$content
            .'</body></html>';

        return response("\xEF\xBB\xBF".$html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * @param  list<string>  $headings
     * @param  list<list<string|int|null>>  $rows
     */
    public function toCsv(string $title, array $headings, array $rows, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($title, $headings, $rows): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [$this->excelSafeText($title)]);
            fputcsv($handle, $headings);

            foreach ($this->normalizeExportRows($rows) as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  list<list<string|int|null>>  $rows
     * @param  list<string>  $columnKeys
     * @return list<list<string|int|null>>
     */
    private function preparePdfRows(array $rows, array $columnKeys): array
    {
        if ($columnKeys === []) {
            return $rows;
        }

        return array_map(function (array $row) use ($columnKeys): array {
            return array_map(function (mixed $cell, int $index) use ($columnKeys): mixed {
                if (($columnKeys[$index] ?? null) !== 'picture' || ! filled($cell)) {
                    return $cell;
                }

                return $this->pictureSourceForPdf((string) $cell);
            }, $row, array_keys($row));
        }, $rows);
    }

    private function pictureSourceForPdf(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || ! str_starts_with($path, '/storage/')) {
            return $url;
        }

        $relativePath = ltrim(substr($path, strlen('/storage/')), '/');
        $fullPath = storage_path('app/public/'.$relativePath);

        if (! is_file($fullPath)) {
            return $url;
        }

        $mime = mime_content_type($fullPath) ?: 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($fullPath));
    }

    /**
     * @param  list<list<string|int|null>>  $rows
     * @return list<list<string>>
     */
    private function normalizeExportRows(array $rows): array
    {
        return array_map(
            fn (array $row): array => array_map(
                fn (string|int|null $cell): string => filled($cell) ? (string) $cell : '',
                $row,
            ),
            $rows,
        );
    }

    private function excelSafeText(string $text): string
    {
        return str_replace(["\u{2014}", "\u{2013}"], ' - ', $text);
    }
}
