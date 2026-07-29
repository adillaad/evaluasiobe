<!DOCTYPE html>
<html>

<head>
    <title>List Profesi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>List Profesi</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Profesi</th>
                <th>Kurikulum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($profesi as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->kurikulum->nama_kurikulum ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
