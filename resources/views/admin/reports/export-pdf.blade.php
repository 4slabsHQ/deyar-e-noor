<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #6b7280; margin-bottom: 16px; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; vertical-align: middle; }
        th { background: #f3f4f6; font-size: 9px; text-transform: uppercase; }
        tr:nth-child(even) td { background: #fafafa; }
        .report-pdf-photo { width: 40px; height: 40px; object-fit: cover; display: block; margin: 0 auto; }
        .report-pdf-photo-cell { width: 48px; text-align: center; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="meta">Generated {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $index => $cell)
                        @php
                            $columnKey = $columnKeys[$index] ?? null;
                        @endphp
                        <td @class(['report-pdf-photo-cell' => $columnKey === 'picture'])>
                            @if ($columnKey === 'picture')
                                @if (filled($cell))
                                    <img src="{{ $cell }}" alt="" class="report-pdf-photo">
                                @else
                                    —
                                @endif
                            @else
                                {{ $cell ?? '—' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
