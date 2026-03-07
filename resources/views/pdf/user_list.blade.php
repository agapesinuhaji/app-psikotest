<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>List User Batch</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h3>{{ $batch->name }} di {{ auth()->user()->name }}</h3>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Email</th>
                <th>NIK</th>
                <th>Tempat, Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Pendidikan Terakhir</th>
                <th>No Telp</th>
                <th>Alamat</th>
                <th>Nama Ayah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->nik }}</td>
                <td>{{ $user->place_of_birth }}, {{ \Carbon\Carbon::parse($user->date_of_birth)->format('d M Y') }}</td>
                <td>{{ $user->gender ?? '-' }}</td>
                <td>{{ $user->last_education ?? '-' }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>{{ $user->address ?? '-' }}</td>
                <td>{{ $user->nama_ayah ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>