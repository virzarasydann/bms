@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-xl mb-3">Laporan Keuangan</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex flex-column">
                                    <div class="d-flex gap-3 ">
                                        <form id="form-export-pdf" method="GET"
                                            action="{{ route('laporanKeuangan.exportPdf') }}">
                                            <input type="hidden" name="hidden_jenis_kategori" id="hidden_jenis_kategori">
                                            <input type="hidden" name="hidden_kategori" id="hidden_kategori">
                                            <input type="hidden" name="hidden_bulan" id="hidden_bulan">
                                            <input type="hidden" name="hidden_tahun" id="hidden_tahun">
                                            <button type="submit" class="btn btn-danger">Export PDF</button>
                                        </form>

                                        <form id="form-export-excel" method="GET"
                                            action="{{ route('laporanKeuangan.exportExcel') }}">
                                            <input type="hidden" name="excel_jenis_kategori" id="excel_jenis_kategori">
                                            <input type="hidden" name="excel_kategori" id="excel_kategori">
                                            <input type="hidden" name="excel_bulan" id="excel_bulan">
                                            <input type="hidden" name="excel_tahun" id="excel_tahun">
                                            <button type="submit" class="btn btn-success">Export Excel</button>
                                        </form>
                                    </div>

                                    <div class="col-md-5 mt-3">
                                        <label for="jenis_kategori">Pilih Jenis</label>

                                        <select class="form-control select2" id="jenis_kategori" name="jenis_kategori">
                                            <option value="" disabled selected hidden>-- Pilih Jenis --</option>
                                            <option value="Pemasukan">Pemasukan</option>
                                            <option value="Pengeluaran">Pengeluaran</option>
                                        </select>

                                    </div>

                                    <div class="col-md-5">
                                        <label for="kategori"></label>

                                        <select class="form-control select2" id="kategori" name="kategori">

                                            <option value="">-- Pilih Kategori --</option>

                                        </select>

                                    </div>
                                    @php
                                        use Carbon\Carbon;

                                        Carbon::setLocale('id'); // Untuk bahasa Indonesia

                                        $bulan = request('bulan'); // format: 01, 02, ..., 12
                                        $tahun = request('tahun'); // format: 2025
                                        $tahunSekarang = Carbon::now()->year;
                                        $tahunMulai = $tahunSekarang - 1;
                                        $tahunAkhir = $tahunSekarang + 5;
                                        $carbonBulan =
                                            $bulan && $tahun
                                                ? Carbon::createFromFormat('Y-m', $tahun . '-' . $bulan)
                                                : null;
                                    @endphp

                                    <div class="col-md-5 mt-3">
                                        <label for="bulan">Pilih Bulan</label>
                                        <select name="bulan" class="form-control">
                                            @for ($i = 1; $i <= 12; $i++)
                                                @php
                                                    $val = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                @endphp
                                                <option value="{{ $val }}"
                                                    {{ request('bulan') == $val ? 'selected' : '' }}>
                                                    {{ Carbon::create()->month($i)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-md-5 mt-3">
                                        <label for="tahun">Pilih Tahun</label>
                                        <select name="tahun" class="form-control">
                                            @for ($y = $tahunMulai; $y <= $tahunAkhir; $y++)
                                                <option value="{{ $y }}"
                                                    {{ request('tahun') == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    @if ($carbonBulan)
                                        <div class="col-md-12 mt-3">
                                            <p><strong>Periode dipilih:</strong> {{ $carbonBulan->translatedFormat('F Y') }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="col-md-2 mt-3">
                                        <button type="submit" id="btnProses" class="btn btn-primary w-100">Proses</button>
                                    </div>
                                </div>
                            </div>

                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <div class="col-md-12 mt-4" id="tabel-wrapper">
            <!-- Tabel akan ditampilkan di sini -->
        </div>

        <!-- /.content -->
    </div>

    <!-- Modal -->



    </div>
@endsection
@push('scripts')
    <script>
        $('#form-export-pdf').on('submit', function(e) {
            e.preventDefault();

            const jenis = $('#jenis_kategori').val();
            const kategori = $('#kategori').val();
            const bulan = $('[name="bulan"]').val();
            const tahun = $('[name="tahun"]').val();

            if (!jenis || !kategori || !bulan || !tahun) {
                alert('Silakan lengkapi pilihan terlebih dahulu sebelum export PDF.');
                return;
            }

            $('#hidden_jenis_kategori').val(jenis);
            $('#hidden_kategori').val(kategori);
            $('#hidden_bulan').val(bulan);
            $('#hidden_tahun').val(tahun);

            this.submit();
        });

        $('#form-export-excel').on('submit', function(e) {
            e.preventDefault();

            const jenis = $('#jenis_kategori').val();
            const kategori = $('#kategori').val();
            const bulan = $('[name="bulan"]').val();
            const tahun = $('[name="tahun"]').val();

            if (!jenis || !kategori || !bulan || !tahun) {
                alert('Silakan lengkapi pilihan terlebih dahulu sebelum export Excel.');
                return;
            }

            $('#excel_jenis_kategori').val(jenis);
            $('#excel_kategori').val(kategori);
            $('#excel_bulan').val(bulan);
            $('#excel_tahun').val(tahun);

            this.submit();
        });


        $(document).ready(function() {
            $('#btnProses').on('click', function() {
                const jenis = $('#jenis_kategori').val();
                const kategori = $('#kategori').val();
                const bulan = $('[name="bulan"]').val();
                const tahun = $('[name="tahun"]').val();

                if (!jenis || !kategori || !bulan || !tahun) {
                    alert('Mohon lengkapi semua pilihan.');
                    return;
                }

                $.ajax({
                    url: '{{ route('laporanKeuangan.store') }}', // pastikan POST route tersedia
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        jenis_kategori: jenis,
                        kategori: kategori,
                        bulan: bulan,
                        tahun: tahun
                    },
                    success: function(res) {
                        if (res.message === 'success') {
                            if (res.data.length === 0) {
                                $('#tabel-wrapper').html(`
                                    <div class="card">
                                        <div class="card-body text-center text-muted">
                                            <h5 class="mb-0">Data tidak ada.</h5>
                                        </div>
                                    </div>
                                `);
                                return;
                            }
                            let rows = '';
                            res.data.forEach((item, index) => {
                                if (jenis === 'Pemasukan') {
                                    rows += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.tanggal_pemasukan ?? '-'}</td>
                                        <td>${item.no_transaksi ?? '-'}</td>
                                        <td>${item.nominal ?? '-'}</td>
                                        <td>${item.tipe ?? '-'}</td>
                                        <td>${item.deskripsi ?? '-'}</td>
                                    </tr>
                                `;
                                } else {
                                    rows += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.pengeluaran?.tanggal_pengeluaran ?? '-'}</td>
                                        <td>${item.pengeluaran?.no_pengeluaran ?? '-'}</td>
                                        <td>${item.nominal ?? '-'}</td>
                                        <td>${item.pengeluaran?.tipe ?? '-'}</td>
                                        <td>${item.pengeluaran?.deskripsi ?? '-'}</td>
                                    </tr>
                                `;
                                }
                            });

                            const thead = jenis === 'Pemasukan' ?
                                `<tr><th>No</th><th>Tanggal</th><th>No Transaksi</th><th>Nominal</th><th>Tipe</th><th>Deskripsi</th></tr>` :
                                `<tr><th>No</th><th>Tanggal</th><th>No Pengeluaran</th><th>Jumlah</th><th>Tipe</th><th>Deskripsi</th></tr>`;

                            $('#tabel-wrapper').html(`
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>${thead}</thead>
                                                    <tbody>${rows}</tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                `);
                        } else {
                            alert('Data gagal diproses.');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat memproses.');
                    }
                });
            });
        });
        $(document).ready(function() {
            $('#jenis_kategori').on('change', function() {
                const jenis_kategori = $(this).val();

                if (jenis_kategori) {
                    $.ajax({
                        url: '{{ route('laporanKeuangan.showJenis') }}',
                        type: 'GET',
                        data: {
                            jenis_kategori: jenis_kategori
                        },
                        success: function(res) {
                            if (res.message === 'success') {
                                let options = `<option value="">-- Pilih Kategori --</option>`;
                                $.each(res.data, function(i, item) {
                                    options +=
                                        `<option value="${item.id}">${item.nama ?? item.kategori ?? item.nama_kategori}</option>`;
                                });
                                $('#kategori').html(options);
                            }
                        },
                        error: function() {
                            alert('Gagal mengambil data kategori.');
                        }
                    });
                } else {

                    $('#kategori').html('<option value="">-- Pilih Kategori --</option>');
                }
            });
        });
    </script>
@endpush
