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
                        <h1 class="text-xl mb-3">Data Pemasukan</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm" id="tombol-tambah">
                                                <i class="fas fa-plus"></i> Tambah Pemasukan
                                            </button>
                                        @endif
                                        <a href="{{ route('cetak-pemasukan') }}" target="_blank"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-file-pdf"></i> Cetak PDF
                                        </a>
                                        <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                            Reload
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped data-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Nama Donatur</th>
                                                <th>Nominal</th>
                                                <th>Kategori</th>
                                                <th>Lampiran</th>
                                                <th width="100px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
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
        <!-- /.content -->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="modalFormLabel">Form Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">

                        <div class="form-group row mb-3">
                            <label for="tanggal_pemasukan" class="col-sm-4 col-form-label">Tanggal</label>
                            <div class="col-sm-3">
                                <input type="date" id="tanggal_pemasukan" class="form-control" name="tanggal_pemasukan"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="no_transaksi" class="col-sm-4 col-form-label">No Transaksi</label>
                            <div class="col-sm-4">
                                <input type="text" id="no_transaksi" class="form-control" name="no_transaksi" readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Donatur</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="nama_lengkap" name="nama_lengkap">
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nominal" class="col-sm-4 col-form-label">Nominal</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend" disabled>
                                        <span class="input-group-text  text-muted">Rp.</span>
                                    </div>
                                    <input type="text" class="form-control" id="nominal" name="nominal"
                                        oninput="formatRupiah(this)">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="file_upload" class="col-sm-4 col-form-label">Upload File</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="file" id="file_upload" name="file_upload">
                                </div>

                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="tipe_saldo" class="col-sm-4 col-form-label">Tipe</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="tipe_saldo" name="tipe_saldo">
                                    <option value="Bank">Bank</option>
                                    <option value="Kas">Kas</option>
                                </select>
                            </div>
                        </div>



                        <div class="form-group row mb-3">
                            <label for="deskripsi" class="col-sm-4 col-form-label">Deskripsi</label>
                            <div class="col-sm-8">
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="kategori_penerimaan_id" class="col-sm-4 col-form-label">Kategori
                                Penerimaan</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="kategori_penerimaan_id"
                                    name="kategori_penerimaan_id"></select>
                            </div>
                        </div>



                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="submitBtn" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLampiran" tabindex="-1" role="dialog" aria-labelledby="modalLampiranLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="modalFormLabel">Lampiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <div id="lampiranPreview"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLampiran" tabindex="-1" aria-labelledby="modalLampiranLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lampiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewLampiran" src="#" alt="Lampiran" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function formatRupiah(input) {
            let value = input.value.replace(/\D/g, '');
            if (!value) {
                input.value = '';
                return;
            }

            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).ready(function() {

            $('#tombol-tambah').on('click', function() {
                const tanggal = $('#tanggal_pemasukan').val();

                // Kalau tanggal sudah dipilih, langsung generate
                if (tanggal) {
                    fetchNomor(tanggal);
                } else {
                    // Atau pakai tanggal sekarang kalau belum diisi
                    fetchNomor();
                }
            });

            function fetchNomor(tanggal = null) {
                $.ajax({
                    url: "{{ route('penerimaan.penomoran') }}",
                    type: "GET",
                    data: tanggal ? {
                        tanggal_pemasukan: tanggal
                    } : {},
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#no_transaksi').val(response.data);
                        } else {
                            alert('Gagal mendapatkan nomor pengeluaran.');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat memuat nomor pengeluaran.');
                    }
                });
            }
            $('#kategori_penerimaan_id').select2({
                placeholder: 'Pilih Kategori',
                ajax: {
                    url: '{{ route('kategori.penerimaan.list') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.nama + ' - ' + item.jenis_kategori
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });

        $(document).ready(function() {
            $('#nama_lengkap').select2({
                ajax: {
                    url: '{{ route('penerimaan.search') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.nama_lengkap,
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });
        });

        $(document).ready(function() {
            $('.select-kategori').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Kategori",
            });
        });

        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('penerimaan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_pemasukan',
                        name: 'tanggal_pemasukan',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'id_donatur',
                        name: 'id_donatur',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'nominal',
                        name: 'nominal',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'kategori_penerimaan',
                        name: 'kategori_penerimaan',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'lampiran',
                        name: 'lampiran',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, ]
            });
        });

        $(document).on('click', '#edit-button', function() {
            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#primary_id').val(response.data.id);
                    $('#tanggal_pemasukan').val(response.data.tanggal_pemasukan);

                    const nominalFormatted = response.data.nominal.toString().replace(
                        /\B(?=(\d{3})+(?!\d))/g, ".");
                    $('#nominal').val(nominalFormatted);

                    let donaturOption = new Option(response.data.donatur.nama_lengkap, response.data
                        .id_donatur, true, true);
                    $('#nama_lengkap').append(donaturOption).trigger('change');

                    let kategoriLabel = response.data.kategori.nama + ' - ' + response.data.kategori
                        .jenis_kategori;
                    let kategoriOption = new Option(kategoriLabel, response.data.kategori_penerimaan, true,
                        true);
                    $('#kategori_penerimaan_id').append(kategoriOption).trigger('change');

                    $('#deskripsi').val(response.data.deskripsi);
                    $('#tipe_saldo').val(response.data.tipe).trigger('change');
                    $('#no_transaksi').val(response.data.no_transaksi);
                }
            });
        });





        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#formData').find('input[type="file"]').val('');
            $('#formData').find('select').val(null).trigger('change');
            $('#primary_id').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        });


        // Simpan / Update data
        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let id = $('#primary_id').val();
            let url = id ? '{{ route('penerimaan.update', ['penerimaan' => ':id']) }}'.replace(':id', id) :
                '{{ route('penerimaan.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let formData = new FormData(this); // Ambil semua data input dalam form
            formData.append('_method', method); // Laravel butuh method override via _method

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false, // Penting untuk FormData
                processData: false, // Penting untuk FormData
                success: function() {
                    $('#modalForm').modal('hide');
                    audio.play();
                    toastr.success("Data telah disimpan!", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    $('.data-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validasi input
                        audio.play();
                        toastr.error("Ada inputan yang salah!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            let input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });
                    } else if (xhr.status === 403) {

                        audio.play();
                        toastr.error(xhr.responseJSON.message || "Akses ditolak!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                    } else {

                        audio.play();
                        toastr.error("Terjadi kesalahan tak terduga!", "ERROR!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                    }
                }

            });
        });

        // Hapus data
        $(document).on('submit', 'form', function(e) {
            if ($(this).has('button.delete-button').length) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        audio.play();
                        toastr.success("Data telah dihapus!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                        $('.data-table').DataTable().ajax.reload();
                    },
                    error: function() {
                        audio.play();
                        toastr.error("Gagal menghapus data.", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                    }
                });

            }
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $(document).on('click', '.btn-lampiran', function() {
            const url = $(this).data('url');
            let ext = url.split('.').pop().toLowerCase();
            let preview = '';

            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                preview =
                    `<img src="${url}" style="max-width: 400px; heigth: auto;" class="img-thumbnail" alt="Lampiran">`;
            } else if (ext === 'pdf') {
                preview = `<iframe src="${url}" width="100%" height="600px"></iframe>`;
            } else {
                preview = `<p>File tidak dapat ditampilkan.</p>`;
            }

            $('#lampiranPreview').html(preview);
            $('#modalLampiran').modal('show');
        });
    </script>
@endpush
