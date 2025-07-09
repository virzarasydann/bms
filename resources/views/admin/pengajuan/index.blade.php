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
                        <h1 class="text-xl mb-3">Data Pengajuan</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Pengajuan</button>
                                        @endif
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
                                                <th width="5%">No</th>
                                                <th width="15%">Tgl Pengajuan</th>
                                                <th>Nama</th>
                                                <th>Alamat</th>
                                                <th>Nama Perekomendasi</th>
                                                <th>Stt Pengajuan</th>
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
                            <label for="tgl_pengajuan" class="col-sm-4 col-form-label">Tanggal Pengajuan</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="date" class="form-control" id="tgl_pengajuan" name="tgl_pengajuan">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="no_pengajuan" class="col-sm-4 col-form-label">No Pengajuan</label>
                            <div class="col-sm-4">
                                <input type="text" id="no_pengajuan" class="form-control" name="no_pengajuan" readonly>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="nama_lengkap" class="col-sm-4 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="alamat" class="col-sm-4 col-form-label">Alamat</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="alamat" name="alamat">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="no_telp" class="col-sm-4 col-form-label">No Telp</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="no_telp" name="no_telp">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="permasalahan" class="col-sm-4 col-form-label">Permasalahan</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <textarea class="form-control" id="permasalahan" name="permasalahan"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="penyelesaian" class="col-sm-4 col-form-label">Penyelesaian</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <textarea class="form-control" id="penyelesaian" name="penyelesaian"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nama_perekomendasi" class="col-sm-4 col-form-label">Nama Perekomendasi</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nama_perekomendasi"
                                        name="nama_perekomendasi">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none" id="status-pengajuan-wrapper">
                            <label for="stt_pengajuan" class="col-sm-4 col-form-label">Status Pengajuan</label>
                            <div class="col-sm-4">
                                <select name="stt_pengajuan" id="stt_pengajuan" class="form-control select-stt">
                                    <option value="0">Pending</option>
                                    <option value="1">Ditolak</option>
                                    <option value="2">Disetujui</option>
                                </select>
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
@endsection
@push('scripts')
    <script>
        $('#modalForm').on('shown.bs.modal', function() {
            const tanggal = $('#tgl_pengajuan').val();

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
                url: "{{ route('pengajuan.penomoran') }}",
                type: "GET",
                data: tanggal ? {
                    tgl_pengajuan: tanggal
                } : {},
                success: function(response) {
                    if (response.status === 'success') {
                        $('#no_pengajuan').val(response.data);
                    } else {
                        alert('Gagal mendapatkan nomor pengeluaran.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat memuat nomor pengeluaran.');
                }
            });
        }
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        @if (session('success'))
            audio.play();
            toastr.success("{{ session('success') }}", "BERHASIL", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right"
            });
        @endif

        @if (session('error'))
            audio.play();
            toastr.error("{{ session('error') }}", "GAGAL!", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right"
            });
        @endif
    </script>


    <script>
        $(document).ready(function() {
            $('.select-stt').select2({
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
                ajax: "{{ route('pengajuan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_pengajuan',
                        name: 'tgl_pengajuan',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'alamat',
                        name: 'alamat',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'nama_perekomendasi',
                        name: 'nama_perekomendasi',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'stt_pengajuan',
                        name: 'stt_pengajuan',
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
            $('#status-pengajuan-wrapper').removeClass('d-none');

            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#primary_id').val(response.data.id);
                    $('#tgl_pengajuan').val(response.data.tgl_pengajuan);
                    $('#nama_lengkap').val(response.data.nama_lengkap);
                    $('#alamat').val(response.data.alamat);
                    $('#no_telp').val(response.data.no_telp);
                    $('#permasalahan').val(response.data.permasalahan);
                    $('#penyelesaian').val(response.data.penyelesaian);
                    $('#nama_perekomendasi').val(response.data.nama_perekomendasi);
                    $('#stt_pengajuan').val(response.data.stt_pengajuan); // ← tambahkan ini

                }
            });
        });





        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        });


        // Simpan / Update data
        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let id = $('#primary_id').val();
            let url = id ? '{{ route('pengajuan.update', ['pengajuan' => ':id']) }}'.replace(':id', id) :
                '{{ route('pengajuan.store') }}';
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
    </script>
@endpush
