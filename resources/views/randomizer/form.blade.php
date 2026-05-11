<!DOCTYPE html>
<html>
<head>
    <title>Randomizer Pasangan Badminton</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">Randomizer Pasangan Turnamen Badminton</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Download Template CSV</h5>
                <p>Gunakan template ini sebagai format data peserta (kolom: <strong>nama</strong>, <strong>pb</strong>).</p>
                <a href="{{ route('randomizer.template') }}" class="btn btn-secondary">Download Template</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Upload File Peserta</h5>
                <form action="{{ route('randomizer.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file_peserta" class="form-label">Pilih file CSV</label>
                        <input type="file" class="form-control" id="file_peserta" name="file_peserta" accept=".csv,.txt" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Acak & Lihat Pasangan</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>