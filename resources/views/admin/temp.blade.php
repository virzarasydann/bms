@extends('admin.layout_admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="text-xl mb-3">Dashboard</h1>

                <div class="row mb-4">

                    {{-- <div class="col-md-3">
                        <div class="small-box p-2 rounded">
                            <div class="d-flex justify-content-start align-items-center" style="gap: 10px">
                                <span class="bg-info p-3 px-4 rounded">
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </span>
                                <div>
                                    <p class="mb-1">Survey & Pengajuan disetujui</p>
                                    <h4 class="mb-0" style="font-weight: 600"> {{ $jumlahSurvey }} /
                                        {{ $jumlahPengajuan }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="small-box p-2 rounded">
                            <div class="d-flex justify-content-start align-items-center" style="gap: 10px">
                                <span class="bg-primary p-3 rounded">
                                    <i class="fas fa-university fa-2x"></i>
                                </span>
                                <div>
                                    <p class="mb-1">Saldo Bank</p>
                                    <h4 class="mb-0" style="font-weight: 600">Rp
                                        {{ number_format($saldoBank, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="small-box p-2 rounded">
                            <div class="d-flex justify-content-start align-items-center" style="gap: 10px">
                                <span class="bg-warning p-3 rounded">
                                    <i class="fas fa-wallet fa-2x"></i>
                                </span>
                                <div>
                                    <p class="mb-1">Saldo Tunai</p>
                                    <h4 class="mb-0" style="font-weight: 600">Rp
                                        {{ number_format($saldoTunai, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="small-box p-2 rounded">
                            <div class="d-flex justify-content-start align-items-center" style="gap: 10px">
                                <span class="bg-success p-3 rounded">
                                    <i class="fas fa-money-bill fa-2x"></i>
                                </span>
                                <div>
                                    <p class="mb-1">Total Saldo</p>
                                    <h4 class="mb-0" style="font-weight: 600">Rp
                                        {{ number_format($totalSaldo, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                </div>

                <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
                    <div class="form-inline">
                        <label for="tahun" class="mr-2">Pilih Tahun:</label>
                        <select name="tahun" id="tahun" class="form-control mr-2 select-tahun"
                            style="min-width: 160px;" onchange="this.form.submit()">
                            @for ($i = now()->year; $i >= now()->year - 3; $i--)
                                <option value="{{ $i }}"
                                    {{ request('tahun', now()->year) == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>

                        <label for="bulan" class="mr-2 ml-3">Pilih Bulan:</label>
                        <select name="bulan" id="bulan" class="form-control mr-2 select-bulan"
                            style="min-width: 160px;" onchange="this.form.submit()">
                            @for ($b = 1; $b <= 12; $b++)
                                <option value="{{ $b }}"
                                    {{ request('bulan', now()->month) == $b ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </form>



                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Posisi saldo per Jenis data</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-warning">
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Data</th>
                                    <th>Pemasukan</th>
                                    <th>Pengeluaran</th>
                                    <th>Saldo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item['no'] }}</td>
                                        <td>{{ $item['jenis_data'] }}</td>
                                        <td class="text-right">
                                            <div class="d-flex justify-content-between">
                                                <span class="me-2">Rp</span>
                                                <span>{{ number_format($item['pemasukan'], 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="d-flex justify-content-between">
                                                <span class="me-2">Rp</span>
                                                <span>{{ number_format($item['pengeluaran'], 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="d-flex justify-content-between">
                                                <span class="me-2">Rp</span>
                                                <span>{{ number_format($item['saldo'], 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#modalDetail{{ $item['id'] }}">
                                                Detail
                                            </button>
                                        </td>

                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>

                        {{-- @foreach ($data as $item)
                            <!-- Modal Detail -->
                            <div class="modal fade" id="modalDetail{{ $item['id'] }}" tabindex="-1" role="dialog"
                                aria-labelledby="modalDetailLabel{{ $item['id'] }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Transaksi: {{ $item['jenis_data'] }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered">
                                                <thead class="bg-warning text-center align-middle">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal</th>
                                                        <th>Deskripsi</th>
                                                        <th>Debet</th>
                                                        <th>Kredit</th>
                                                        <th>Saldo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php

                                                        $semuaTransaksi = collect();

                                                        // Loop pemasukan
                                                        foreach ($item['transaksi_pemasukan'] as $pemasukan) {
                                                            $semuaTransaksi->push([
                                                                'tanggal' => $pemasukan->tanggal_pemasukan,
                                                                'deskripsi' =>
                                                                    'Pemasukan dari: ' .
                                                                    ($pemasukan->donatur->nama_lengkap ?? 'N/A'),
                                                                'debet' => $pemasukan->nominal,
                                                                'kredit' => 0,
                                                            ]);
                                                        }

                                                        // Loop pengeluaran
                                                        foreach ($item['transaksi_pengeluaran'] as $pengeluaran) {
                                                            $tanggalPengeluaran =
                                                                $pengeluaran->pengeluaran->tanggal_pengeluaran ?? null;
                                                            $namaMustahik =
                                                                $pengeluaran->pengeluaran->mustahik->nama_lengkap ??
                                                                'N/A';

                                                            $semuaTransaksi->push([
                                                                'tanggal' => $tanggalPengeluaran,
                                                                'deskripsi' => 'Pengeluaran ke: ' . $namaMustahik,
                                                                'debet' => 0,
                                                                'kredit' => $pengeluaran->nominal,
                                                            ]);
                                                        }

                                                        // Urutkan berdasarkan tanggal
                                                        $semuaTransaksi = $semuaTransaksi->sortBy('tanggal')->values();

                                                        // Saldo awal
                                                        $saldo = 0;
                                                    @endphp

                                                    @foreach ($semuaTransaksi as $i => $transaksi)
                                                        @php
                                                            $saldo += $transaksi['debet'] - $transaksi['kredit'];
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $i + 1 }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($transaksi['tanggal'])->translatedFormat('d F Y') }}
                                                            </td>
                                                            <td>{{ $transaksi['deskripsi'] }}</td>
                                                            <td class="text-end">
                                                                @if ($transaksi['debet'] > 0)
                                                                    Rp
                                                                    {{ number_format($transaksi['debet'], 0, ',', '.') }}
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                @if ($transaksi['kredit'] > 0)
                                                                    Rp
                                                                    {{ number_format($transaksi['kredit'], 0, ',', '.') }}
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($saldo, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>



                                    </div>
                                </div>
                            </div>
                        @endforeach --}}


                    </div>
                </div>

            </div><!-- /.container-fluid -->
        </div>

        <section class="content">
            <div class="container-fluid">




            </div>
            <!-- /.row -->
    </div><!-- /.container-fluid -->
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-tahun').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Tahun",
            });
        });
    </script>
@endpush
