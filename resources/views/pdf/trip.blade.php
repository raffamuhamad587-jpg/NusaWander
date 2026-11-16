<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $trip->judul }} - NUSA WANDER Itinerary</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.5;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #003527;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #003527;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .tagline {
            font-size: 10px;
            color: #666666;
        }
        .trip-title {
            font-size: 18px;
            font-weight: bold;
            color: #003527;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .trip-meta {
            font-size: 11px;
            color: #555555;
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #003527;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #eefdf5;
            color: #003527;
            font-weight: bold;
        }
        .total-box {
            background-color: #eefdf5;
            border: 1px solid #174c3c;
            padding: 12px 15px;
            border-radius: 6px;
            margin-top: 20px;
            text-align: right;
        }
        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #003527;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #888888;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="border: none; margin: 0;">
            <tr style="border: none;">
                <td style="border: none; padding: 0;">
                    <div class="logo">NUSA WANDER</div>
                    <div class="tagline">Smart Travel &amp; Budget Planner &bull; Explore Indonesia, Your Way</div>
                </td>
                <td style="border: none; padding: 0; text-align: right; vertical-align: bottom;">
                    <span style="font-size: 10px; color: #888;">Tanggal Cetak: {{ date('d F Y') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="trip-title">{{ $trip->judul }}</div>
    <div class="trip-meta">
        <strong>Penyusun:</strong> {{ $trip->user->name }} ({{ $trip->user->email }}) &bull;
        <strong>Destinasi Utama:</strong> {{ $trip->destination->nama }} ({{ $trip->destination->kota }})
    </div>
    @if($trip->deskripsi)
        <p style="color: #555; margin-bottom: 20px;">{{ $trip->deskripsi }}</p>
    @endif

    <div class="section-title">Jadwal Rencana Perjalanan (Itinerary)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">No</th>
                <th style="width: 40%;">Destinasi</th>
                <th style="width: 25%;">Kota / Lokasi</th>
                <th style="width: 27%; text-align: right;">Estimasi Tiket</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trip->itineraryItems as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $item->urutan }}</td>
                    <td>
                        <strong>{{ $item->judul }}</strong>
                        @if($item->catatan)
                            <br><small style="color: #666;">{{ $item->catatan }}</small>
                        @endif
                    </td>
                    <td>{{ $item->destination->kota ?? '-' }}</td>
                    <td style="text-align: right;">
                        Rp {{ number_format($item->estimasi_harga, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #888;">Belum ada jadwal destinasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($trip->costs->count() > 0)
        <div class="section-title">Rincian Anggaran Tambahan</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Keterangan</th>
                    <th style="width: 25%; text-align: right;">Per Orang</th>
                    <th style="width: 25%; text-align: right;">Per Kelompok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trip->costs as $cost)
                    <tr>
                        <td>{{ $cost->keterangan }}</td>
                        <td style="text-align: right;">Rp {{ number_format($cost->harga_per_orang, 2, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($cost->harga_per_kelompok, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="total-box">
        <span style="font-size: 12px; color: #555;">Estimasi Total Anggaran:</span>
        <div class="total-amount">Rp {{ number_format($trip->total_estimasi_harga, 2, ',', '.') }}</div>
    </div>

    <div class="footer">
        Dihasilkan secara otomatis oleh Nusa Wander &bull; Simpan file ini sebagai panduan perjalanan Anda.
    </div>
</body>
</html>
