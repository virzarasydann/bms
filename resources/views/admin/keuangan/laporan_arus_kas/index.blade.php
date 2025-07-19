@extends('admin.layout_admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="text-xl mb-3">Laporan Arus Kas</h1>

                <div class="row mb-4">


                </div>

                <form id="formData" class="mb-3">
                    <div class="form-inline">
                        <label for="tahun" class="mr-2">Pilih Tahun:</label>
                        <select name="tahun" id="tahun" class="form-control mr-2 select-tahun"
                            style="min-width: 160px;">
                            @for ($i = now()->year; $i >= now()->year - 3; $i--)
                                <option value="{{ $i }}"
                                    {{ request('tahun', now()->year) == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>

                        <label for="bulan" class="mr-2 ml-3">Pilih Bulan:</label>
                        <select name="bulan" id="bulan" class="form-control mr-2 select-bulan"
                            style="min-width: 160px;">
                            @for ($b = 1; $b <= 12; $b++)
                                <option value="{{ $b }}"
                                    {{ request('bulan', now()->month) == $b ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>

                        <label for="id_bank" class="mr-2 ml-3">Pilih Rekening:</label>
                        <select name="id_bank" id="id_bank" class="form-control mr-2 select-2" style="min-width: 160px;">
                            <option value="">-- Pilih Rekening --</option>
                            @foreach ($dataBank as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nama_bank }}</option>
                            @endforeach
                        </select>

                        <button type="button" id="btnFilter" class="btn btn-primary ml-3">Filter</button>
                    </div>
                </form>



                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-end" style="gap: 3px">


                            <button type="button" id="export-pdf" data-url="{{ route('laporan.aruskas.export.pdf') }}"
                                class="btn btn-danger px-3 py-1 btn-xs">
                                Export PDF
                            </button>
                            <button type="button" id="export-excel" data-url="{{ route('laporan.aruskas.export.excel') }}"
                                class="btn btn-success px-3 py-1 btn-xs">
                                Export Excel
                            </button>


                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped" id="laporan-table">
                            <thead class="bg-warning">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Jenis</th>
                                    <th>Nominal</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>




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
        $('#export-pdf, #export-excel').on('click', function() {
            const bankId = $('#id_bank').val();
            const bulan = $('#bulan').val();
            const tahun = $('#tahun').val();

            if (!bankId) {
                toastr.error("Silakan pilih bank terlebih dahulu.");
                return;
            }

            let url = $(this).data('url');
            window.open(`${url}?id_bank=${bankId}&bulan=${bulan}&tahun=${tahun}`, '_blank');
        });

        $(document).ready(function() {
            let table;

            function loadTable() {
                const tahun = $('#tahun').val();
                const bulan = $('#bulan').val();
                const id_bank = $('#id_bank').val();

                table = $('#laporan-table').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('laporan.aruskas.index') }}",
                        data: {
                            tahun: tahun,
                            bulan: bulan,
                            id_bank: id_bank
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tanggal',
                            name: 'tanggal'
                        },
                        {
                            data: 'kategori',
                            name: 'kategori'
                        },
                        {
                            data: 'jenis',
                            name: 'jenis'
                        },
                        {
                            data: 'nominal',
                            name: 'nominal'
                        },
                    ]
                });
            }


            // loadTable();


            $('#btnFilter').click(function() {
                loadTable();
            });
        });
    </script>
@endpush
