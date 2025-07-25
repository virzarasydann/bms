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

                {{-- <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
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
                </form> --}}



                <div class="row">
                    <!-- Card Kiri: Project -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h3 class="card-title">Project Belum Selesai</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Project</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projects as $index => $project)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $project->nama_project }}</td>
                                                <td>{{ \Carbon\Carbon::parse($project->tanggal_selesai)->translatedFormat('d - F - Y') }}</td>
                                                <td>
                                                    <a href="{{ route('dashboard.project.detail', $project->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        Detail
                                                    </a>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Card Kanan: Helpdesk -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h3 class="card-title">Helpdesk Belum Close</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Project</th>
                                            <th>Status</th>
                                            {{-- <th>Deskripsi</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($helpdesks as $index => $helpdesk)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $helpdesk->project->nama_project ?? '-' }}</td>
                                                <td>
                                                @php
                                                    $status = strtolower($helpdesk->status_komplen);
                                                    $badgeClass = match ($status) {
                                                        'open' => 'primary',
                                                        'progress' => 'success',
                                                        'close' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">
                                                    {{ ucfirst($helpdesk->status_komplen) }}
                                                </span>
                                            </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h3 class="card-title">Sewa Dibawah 50 hari</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Layanan</th>
                                            <th>Tanggal Expired</th>
                                            {{-- <th>Deskripsi</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($sewaExpiring->isEmpty())
                                            <tr>
                                                <td colspan="3">Tidak ada data sewa yang akan habis dalam 50 hari</td>
                                            </tr>
                                        @else
                                            @foreach ($sewaExpiring as $index => $sewa)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $sewa->nama_layanan ?? '-' }}</td>
                                                     <td>{{ \Carbon\Carbon::parse($sewa->tgl_expired)->translatedFormat('d - F - Y') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    <script></script>
@endpush
