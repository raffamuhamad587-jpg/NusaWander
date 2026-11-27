<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $trip->nama_trip }} - NUSA WANDER Itinerary</title>
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
            margin-bottom: 6px;
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
            margin-top: 22px;
            margin-bottom: 10px;
        }
        .day-header {
            background-color: #eefdf5;
            color: #003527;
            font-weight: bold;
            padding: 6px 10px;
            margin-top: 12px;
            border-left: 3px solid #174c3c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 14px;
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 7px 9px;
            text-align: left;
        }
        th {
            background-color: #eefdf5;
            color: #003527;
            font-weight: bold;
        }
        .summary-box {
            background-color: #eefdf5;
            border: 1px solid #174c3c;
            padding: 12px 15px;
            margin-top: 18px;
        }
        .summary-row {
            margin-bottom: 4px;
        }
        .over-budget {
            color: #b91c1c;
            font-weight: bold;
        }
        .safe-budget {
            color: #047857;
            font-weight: bold;
        }
        .muted {
            color: #777777;
            font-style: italic;
        }
        .footer {
            margin-top: 36px;
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

    <div class="trip-title">{{ $trip->nama_trip }}</div>
    <div class="trip-meta">
        <strong>Penyusun:</strong> {{ $trip->user->name }} &bull;
        <strong>Tujuan:</strong> {{ $trip->wilayah_tujuan }} &bull;
        <strong>Tanggal Mulai:</strong> {{ \Illuminate\Support\Carbon::parse($trip->tanggal_mulai)->format('d M Y') }} &bull;
        <strong>Durasi:</strong> {{ $trip->durasi_hari }} Hari &bull;
        <strong>Peserta:</strong> {{ $trip->jumlah_peserta }} Orang
    </div>

    <div class="section-title">Susunan Itinerary Per Hari</div>
    @for($day = 1; $day <= $trip->durasi_hari; $day++)
        @php
            $dayItems = $trip->itineraryItems->where('hari_ke', $day)->sortBy('urutan_kunjungan');
        @endphp
        <div class="day-header">Hari {{ $day }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%; text-align: center;">Urutan</th>
                    <th style="width: 38%;">Destinasi</th>
                    <th style="width: 24%;">Kota &amp; Kategori</th>
                    <th style="width: 28%;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dayItems as $item)
                    <tr>
                        <td style="text-align: center;">{{ $item->urutan_kunjungan }}</td>
                        <td><strong>{{ $item->destination->nama ?? '-' }}</strong></td>
                        <td>{{ $item->destination->kota ?? '-' }} ({{ $item->destination->category->nama_kategori ?? '-' }})</td>
                        <td>{{ $item->catatan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted" style="text-align: center;">Belum ada destinasi dijadwalkan pada Hari {{ $day }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endfor

    <div class="section-title">Rincian Estimasi Biaya</div>
    <table>
        <thead>
            <tr>
                <th style="width: 32%;">Komponen Biaya</th>
                <th style="width: 14%;">Jenis</th>
                <th style="width: 16%;">Dasar</th>
                <th style="width: 10%; text-align: center;">Jumlah</th>
                <th style="width: 28%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trip->costs as $cost)
                <tr>
                    <td>{{ $cost->nama_biaya }}</td>
                    <td style="text-transform: capitalize;">{{ $cost->jenis_biaya }}</td>
                    <td>{{ str_replace('_', ' ', $cost->dasar_perhitungan) }}</td>
                    <td style="text-align: center;">{{ $cost->jumlah }}</td>
                    <td style="text-align: right;">
                        @if($cost->subtotal === null)
                            <span class="muted">Belum dihitung</span>
                        @else
                            Rp {{ number_format($cost->subtotal, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="muted" style="text-align: center;">Belum ada komponen biaya tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row"><strong>Anggaran Trip:</strong> Rp {{ number_format($budgetSummary['anggaran'], 0, ',', '.') }}</div>
        <div class="summary-row"><strong>Total Estimasi Tersedia:</strong> Rp {{ number_format($budgetSummary['total_estimasi'], 0, ',', '.') }}</div>
        <div class="summary-row">
            <strong>Sisa Anggaran:</strong>
            <span class="{{ $budgetSummary['is_over_budget'] ? 'over-budget' : 'safe-budget' }}">
                Rp {{ number_format($budgetSummary['sisa_anggaran'], 0, ',', '.') }}
                ({{ $budgetSummary['is_over_budget'] ? 'Melebihi Anggaran' : 'Dalam Batas Anggaran' }})
            </span>
        </div>
        @if($budgetSummary['belum_dihitung_count'] > 0)
            <div class="muted" style="margin-top: 6px;">
                *Terdapat {{ $budgetSummary['belum_dihitung_count'] }} komponen biaya bertanda "Belum dihitung" yang belum masuk ke total estimasi.
            </div>
        @endif
    </div>

    <div class="footer">
        Dihasilkan oleh NUSA WANDER &bull; Harga pada sistem adalah estimasi yang dikelola Admin, bukan harga real-time.
    </div>
</body>
</html>
