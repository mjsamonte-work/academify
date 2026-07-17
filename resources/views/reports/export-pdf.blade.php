<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ $data['title'] }}</title>
        <style>
            body { color: #18181b; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
            h1 { font-size: 18px; margin: 0 0 4px; }
            p { color: #52525b; margin: 0 0 18px; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #d4d4d8; padding: 7px; text-align: left; vertical-align: top; }
            th { background: #f4f4f5; color: #3f3f46; font-size: 9px; text-transform: uppercase; }
        </style>
    </head>
    <body>
        <h1>{{ $data['title'] }}</h1>
        <p>Academify formal report export</p>

        <table>
            <thead>
                <tr>
                    @foreach($data['headings'] as $heading)
                        <th>{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($data['rows'] as $row)
                    <tr>
                        @foreach($row as $value)
                            <td>{{ filled($value) ? $value : 'Not set' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($data['headings']) }}">No report records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
