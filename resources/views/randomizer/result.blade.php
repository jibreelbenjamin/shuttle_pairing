<!DOCTYPE html>
<html>
<head>
    <title>Hasil Randomizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">Hasil Pengacakan Pasangan</h1>

        @if(count($hasil) > 0)
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Pasangan / Status</th>
                        <th>Pemain 1</th>
                        <th>Pemain 2</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasil as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($item['tipe'] == 'pasangan')
                                    <span class="badge bg-success">Pasangan</span>
                                @else
                                    <span class="badge bg-warning text-dark">BY (Tunggal)</span>
                                @endif
                            </td>
                            <td>{{ $item['pemain1']['nama'] }} ({{ $item['pemain1']['pb'] }})</td>
                            <td>
                                @if($item['pemain2'])
                                    {{ $item['pemain2']['nama'] }} ({{ $item['pemain2']['pb'] }})
                                @else
                                    <em>BY</em>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-warning">Tidak ada data peserta.</div>
        @endif

        <a href="{{ route('randomizer.form') }}" class="btn btn-primary mt-3">Kembali & Upload Lagi</a>
    </div>
</body>
</html>