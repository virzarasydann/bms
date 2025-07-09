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
                        <h1 class="text-xl mb-3">Data Mustahik</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                         @if ($permissions['tambah'])
                                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalForm"><i class="fas fa-plus"></i>
                                            Tambah Mustahik</button>
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
                                                <th>No</th>
                                                <th>Nama Lengkap</th>
                                                <th>Alamat</th>
                                                <th>Jenis Kelamin</th>
                                                <th>No Telpon</th>
                                                <th>Nik</th>
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
                        <!-- Nama Lengkap -->
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label" for="nama_lengkap">Nama Lengkap</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap">
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label" for="alamat">Alamat</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="alamat" name="alamat">
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label" for="jenis_kelamin">Jenis Kelamin</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- No Telepon -->
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label" for="no_telp">No Telepon</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="no_telp" name="no_telp">
                            </div>
                        </div>

                        <!-- NIK -->
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label" for="nik">NIK</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="nik" name="nik">
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
        $(document).ready(function() {
             var permissions = @json($permissions);
             var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);
            // Inisialisasi DataTable
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,

                ajax: "{{ route('mustahik.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'alamat',
                        name: 'alamat',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_kelamin',
                        name: 'jenis_kelamin',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no_telp',
                        name: 'no_telp',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nik',
                        name: 'nik',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Ketika klik tombol edit
            $(document).on('click', '#edit-button', function() {
                const url = $(this).data('url');

                $.get(url, function(data) {
                    $('#primary_id').val(data.id);
                    $('#nama_lengkap').val(data.nama_lengkap);
                    $('#alamat').val(data.alamat);
                    $('#jenis_kelamin').val(data.jenis_kelamin).trigger('change');
                    $('#no_telp').val(data.no_telp);
                    $('#nik').val(data.nik);
                    $('#modalForm').modal('show');
                });
            });

            // Reset form saat modal ditutup
            $('#modalForm').on('hidden.bs.modal', function() {
                $('#formData')[0].reset();
                $('#primary_id').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            var audio = new Audio('{{ asset('audio/notification.ogg') }}');
            // Simpan atau Update data
            $('#formData').on('submit', function(e) {
                e.preventDefault();

                let id = $('#primary_id').val();
                let url = id ? '{{ route('mustahik.update', ':id') }}'.replace(':id', id) :
                    '{{ route('mustahik.store') }}';
                let method = id ? 'PUT' : 'POST';

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let formData = new FormData(this);
                formData.append('_method', method);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        audio.play();
                        $('#modalForm').modal('hide');
                        toastr.success("Data berhasil disimpan!", "Sukses");
                        table.ajax.reload();
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

            // Konfirmasi dan hapus data
            $(document).on('click', '.delete-button', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
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
            });
        });
    </script>
@endpush
