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
                        <h1 class="text-xl mb-3">Laporan Jurnal</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex flex-column">
                                    <div class="d-flex gap-3 ">
                                        <form id="form-export-pdf" method="GET"
                                            action="{{ route('laporanJurnal.exportPdf') }}">
                                            <input type="hidden" name="hidden_tipe" id="hidden_tipe">
                                            <input type="hidden" name="hidden_bulan" id="hidden_bulan">
                                            <input type="hidden" name="hidden_tahun" id="hidden_tahun">
                                            <button type="submit" class="btn btn-danger">Export PDF</button>
                                        </form>

                                        <form id="form-export-excel" method="GET"
                                            action="{{ route('laporanJurnal.exportExcel') }}">
                                            <input type="hidden" name="excel_tipe" id="excel_tipe">
                                            <input type="hidden" name="excel_bulan" id="excel_bulan">
                                            <input type="hidden" name="excel_tahun" id="excel_tahun">
                                            <button type="submit" class="btn btn-success">Export Excel</button>
                                        </form>
                                    </div>

                                    <div class="col-md-5 mt-3">
                                        <label for="tipe">Pilih Tipe</label>

                                        <select class="form-control select2" id="tipe" name="tipe">
                                            <option value="" disabled selected hidden>-- Pilih Tipe --</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Kas">Kas</option>
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
        // EXPORT PDF
        $('#form-export-pdf').on('submit', function(e) {
            e.preventDefault();

            const tipe = $('#tipe').val();
            const bulan = $('[name="bulan"]').val();
            const tahun = $('[name="tahun"]').val();

            if (!tipe || !bulan || !tahun) {
                alert('Silakan lengkapi pilihan terlebih dahulu sebelum export PDF.');
                return;
            }

            $('#hidden_tipe').val(tipe);
            $('#hidden_bulan').val(bulan);
            $('#hidden_tahun').val(tahun);

            this.submit();
        });

        // EXPORT EXCEL
        $('#form-export-excel').on('submit', function(e) {
            e.preventDefault();

            const tipe = $('#tipe').val();
            const bulan = $('[name="bulan"]').val();
            const tahun = $('[name="tahun"]').val();

            if (!tipe || !bulan || !tahun) {
                alert('Silakan lengkapi pilihan terlebih dahulu sebelum export Excel.');
                return;
            }

            $('#excel_tipe').val(tipe);
            $('#excel_bulan').val(bulan);
            $('#excel_tahun').val(tahun);

            this.submit();
        });

        // PROSES TABEL
        $(document).ready(function() {
            $('#btnProses').on('click', function() {
                const tipe = $('#tipe').val();
                const bulan = $('[name="bulan"]').val();
                const tahun = $('[name="tahun"]').val();

                if (!tipe || !bulan || !tahun) {
                    alert('Mohon lengkapi semua pilihan.');
                    return;
                }

                $.ajax({
                    url: '{{ route('laporanJurnal.store') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        tipe: tipe,
                        bulan: bulan,
                        tahun: tahun
                    },
                    success: function(res) {
                        if (res.message === 'success') {
                            const pemasukan = res.data.pemasukan;
                            const pengeluaran = res.data.pengeluaran;

                            if (pemasukan.length === 0 && pengeluaran.length === 0) {
                                $('#tabel-wrapper').html(`
                            <div class="card">
                                <div class="card-body text-center text-muted">
                                    <h5 class="mb-0">Data tidak ada.</h5>
                                </div>
                            </div>
                        `);
                                return;
                            }

                            // Gabungkan data & beri penanda tipe
                            const allData = [
                                ...pemasukan.map(item => ({
                                    ...item,
                                    _tipe: 'pemasukan'
                                })),
                                ...pengeluaran.map(item => ({
                                    ...item,
                                    _tipe: 'pengeluaran'
                                }))
                            ];

                            // Urutkan berdasarkan tanggal (opsional, jika diperlukan)
                            allData.sort((a, b) => {
                                const tglA = a.tanggal_pemasukan || a.pengeluaran
                                    ?.tanggal_pengeluaran;
                                const tglB = b.tanggal_pemasukan || b.pengeluaran
                                    ?.tanggal_pengeluaran;
                                return new Date(tglA) - new Date(tglB);
                            });

                            let rows = '';
                            let saldo = res.data.saldo_awal ?? 0; 

                            allData.forEach((item, index) => {
                                const isPemasukan = item._tipe === 'pemasukan';

                                const tanggal = isPemasukan ?
                                    item.tanggal_pemasukan :
                                    item.pengeluaran?.tanggal_pengeluaran;

                                const penomoran = isPemasukan ?
                                    item.no_transaksi :
                                    item.pengeluaran?.no_pengeluaran;

                                const tipe = isPemasukan ?
                                    item.tipe :
                                    item.pengeluaran?.tipe;

                                const deskripsi = isPemasukan ?
                                    item.deskripsi :
                                    item.pengeluaran?.deskripsi;

                                const nominal = item.nominal ?? 0;

                                // Saldo update
                                if (isPemasukan) {
                                    saldo += nominal;
                                } else {
                                    saldo -= nominal;
                                }

                                rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${tanggal ?? '-'}</td>
                                <td>${penomoran ?? '-'}</td>
                                <td class="text-end">${isPemasukan ? formatRupiah(nominal) : '-'}</td>
                                <td class="text-end">${!isPemasukan ? formatRupiah(nominal) : '-'}</td>
                                <td>${tipe ?? '-'}</td>
                                <td>${deskripsi ?? '-'}</td>
                                <td class="text-end fw-bold">${formatRupiah(saldo)}</td>
                            </tr>
                        `;
                            });

                            const thead = `
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Penomoran</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Saldo</th>
                        </tr>
                    `;

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

            // Format rupiah helper
            function formatRupiah(angka) {
                if (!angka) return 'Rp0';
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }
        });
    </script>
@endpush
