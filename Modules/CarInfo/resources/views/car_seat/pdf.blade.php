<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seat Types</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Selected Seat Types</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Seat Name</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seats as $seat)
                <tr>
                    <td>{{ $seat->id }}</td>
                    <td>{{ $seat->seat_type }}</td>
                    <td>{{ $seat->status }}</td>
                    <td>{{ $seat->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
