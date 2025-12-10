<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Pemeriksaan Psikologi</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            margin: 40px;
        }

        h2, h3 {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            page-break-inside: avoid;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .no-border td {
            border: none !important;
        }

        .category {
            width: 30px;
            text-align: center;
        }

        .small-note {
            font-size: 11px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- --------------------- HEADER --------------------- -->
<h2>RINGKASAN</h2>
<h3>HASIL PEMERIKSAAN PSIKOLOGI</h3>
<p style="text-align:center;font-style:italic;margin-bottom:20px">
    (Resume Of Psychological Assessment)
</p>

<!-- --------------------- IDENTITAS PASIEN --------------------- -->
<table>
    <tr>
        <td width="35%">
            1. Nama Lengkap Pasien<br>
            <i>(Patient's Full Name)</i>
        </td>
        <td>{{ $user->name }}</td>
    </tr>
    <tr>
        <td>2. Tempat & Tanggal Lahir<br><i>(Place & Date of Birth)</i></td>
        <td>{{ $user->birth_place }}, {{ $user->birth_date }}</td>
    </tr>
    <tr>
        <td>3. Usia<br><i>(Age)</i></td>
        <td>{{ $user->age }} Tahun</td>
    </tr>
    <tr>
        <td>4. Jenis Kelamin<br><i>(Gender)</i></td>
        <td>{{ $user->gender }}</td>
    </tr>
    <tr>
        <td>5. Pendidikan Terakhir<br><i>(Latest Educational Background)</i></td>
        <td>{{ $user->education }}</td>
    </tr>
</table>

<!-- --------------------- IDENTITAS PSIKOLOG --------------------- -->
<table style="margin-top:25px">
    <tr>
        <td width="35%">1. Nama Psikolog Pemeriksa<br><i>(Psychologist's Name)</i></td>
        <td>{{ $corporate_identity->psikolog ?? '-' }}</td>
    </tr>
    <tr>
        <td>2. Nama Fasyankes / Lembaga<br><i>(Clinic/Hospital)</i></td>
        <td>{{ $corporate_identity->clinic_name ?? 'Klinik Spesialis Anugerah Ibu' }}</td>
    </tr>
    <tr>
        <td>3. Alamat Fasyankes / Lembaga<br><i>(Clinic/Hospital Address)</i></td>
        <td>{{ $corporate_identity->address ?? '-' }}</td>
    </tr>
    <tr>
        <td>4. Tanggal Pemeriksaan<br><i>(Assessment Date)</i></td>
        <td>{{ $batch->start_time ?? '-' }}</td>
    </tr>
</table>

<!-- --------------------- HALAMAN BARU --------------------- -->
<div style="page-break-before: always;"></div>

<!-- --------------------- SPM --------------------- -->
<h3>1. Kemampuan Intelektual (SPM)</h3>

<table>
    <tr>
        <th>ASPEK YANG DINILAI</th>
        <th>URAIAN</th>
        <th class="category">R</th>
        <th class="category">K</th>
        <th class="category">C</th>
        <th class="category">B</th>
        <th class="category">T</th>
    </tr>

    @foreach(($spm ?? []) as $item)
        <tr>
            <td>{{ $item['label'] ?? '-' }}</td>
            <td>{{ $item['desc'] ?? '-' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'R' ? 'x' : '' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'K' ? 'x' : '' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'C' ? 'x' : '' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'B' ? 'x' : '' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'T' ? 'x' : '' }}</td>
        </tr>
    @endforeach
</table>

<p class="small-note"><b>Keterangan:</b> R: Rendah | K: Kurang | C: Cukup | B: Baik | T: Tinggi</p>
<p><b>Kesimpulan:</b> {{ $spmConclusion ?? '-' }}</p>

<!-- --------------------- PAPIKOSTIK --------------------- -->
<h3 style="margin-top:35px">2. Sikap dan Cara Kerja (Papikostick)</h3>

<table>
    <tr>
        <th>ASPEK YANG DINILAI</th>
        <th>URAIAN</th>
        <th class="category">R</th>
        <th class="category">K</th>
        <th class="category">C</th>
        <th class="category">B</th>
        <th class="category">T</th>
    </tr>

    @foreach(($papikostik ?? []) as $item)
        <tr>
            <td>{{ $item['label'] ?? '-' }}</td>
            <td>{{ $item['desc'] ?? '-' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'R' ? 'x' : '' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'K' ? 'x' : '' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'C' ? 'x' : '' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'B' ? 'x' : '' }}</td>
            <td class="category">{{ ($item['cat'] ?? '') == 'T' ? 'x' : '' }}</td>
        </tr>
    @endforeach
</table>

<p class="small-note"><b>Keterangan:</b> R: Rendah | K: Kurang | C: Cukup | B: Baik | T: Tinggi</p>
<p><b>Kesimpulan:</b> {{ $papikostikConclusion ?? '-' }}</p>

<!-- --------------------- HALAMAN TERAKHIR --------------------- -->
<div style="page-break-before: always;"></div>

<h3>Kesimpulan akhir:</h3>
<p style="margin-top:10px"><b>{{ $finalConclusion ?? '-' }}</b></p>

<br><br>

<table class="no-border">
    <tr>
        <td width="30%">Tanggal</td>
        <td>: {{ $finalDate ?? '-' }}</td>
    </tr>
    <tr>
        <td>Tanda Tangan</td>
        <td>:
            @if(!empty($signatureQrcode))
                <img src="{{ $signatureQrcode }}" width="80">
            @else
                -
            @endif
        </td>
    </tr>
    <tr>
        <td>Nama Psikolog</td>
        <td>: {{ $psikolog->name ?? '-' }}</td>
    </tr>
    <tr>
        <td>Nomor STR/SIK</td>
        <td>: {{ $psikolog->str ?? '-' }}</td>
    </tr>
    <tr>
        <td>Nomor SIPP/SIPPK</td>
        <td>: {{ $psikolog->sipp ?? '-' }}</td>
    </tr>
</table>

</body>
</html>
