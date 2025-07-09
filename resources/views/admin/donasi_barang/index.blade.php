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
                        <h1 class="text-xl mb-3">Data Donasi Barang</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                           @if ($permissions['tambah'])
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalForm"><i
                                                class="fas fa-plus"></i>
                                            Tambah Donasi Barang</button>
                                          @endif

                                        <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                            Reload
                                        </button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tgl Donasi</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Nama Donatur</th>
                                            <th>Keterangan</th>
                                            <th width="100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
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
                            <label for="tgl_donasi" class="col-sm-4 col-form-label">Tanggal Donasi</label>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_donasi" class="form-control" name="tgl_donasi" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nama_barang" class="col-sm-4 col-form-label">Nama Barang</label>
                            <div class="col-sm-5">
                                <input type="text" id="nama_barang" class="form-control" name="nama_barang">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="jumlah" class="col-sm-4 col-form-label">Jumlah</label>
                            <div class="col-sm-3">
                                <input type="number" id="jumlah" class="form-control" name="jumlah">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="satuan" class="col-sm-4 col-form-label">Satuan</label>
                            <div class="col-sm-4">
                                <input type="text" id="satuan" class="form-control" name="satuan">
                            </div>
                        </div>

                         <div class="form-group row mb-3">
                            <label for="donatur_id" class="col-sm-4 col-form-label">Nama Donatur</label>
                            <div class="col-sm-5">
                                <select class="form-control select2" id="donatur_id"
                                    name="donatur_id"></select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="keterangan" class="col-sm-4 col-form-label">Keterangan</label>
                            <div class="col-sm-5">
                                <textarea  id="keterangan" class="form-control" name="keterangan"></textarea>
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

        $(document).ready(function() {
            $('#donatur_id').select2({
            placeholder: 'Pilih Donatur',
            ajax: {
                url: '{{ route('donaturList') }}',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.nama_lengkap
                            };
                        })
                    };
                },
                cache: true
            }
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
                ajax: "{{ route('donasiBarang.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_donasi',
                        name: 'tgl_donasi',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_donatur',
                        name: 'nama_donatur',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
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
                    $('#tgl_donasi').val(response.data.tgl_donasi);
                    $('#nama_barang').val(response.data.nama_barang);
                    $('#jumlah').val(response.data.jumlah);
                    $('#satuan').val(response.data.satuan);
                    $('#keterangan').val(response.data.keterangan);
                    var donaturOption = new Option(response.data.nama_donatur, response.data.id, true, true);
                    $('#donatur_id').append(donaturOption).trigger('change');
                }
            });
        });


        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.select-kategori').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        });

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let id = $('#primary_id').val();
            let url = id ? '{{ route('donasiBarang.update', ['donasiBarang' => ':id']) }}'.replace(':id', id) :
                '{{ route('donasiBarang.store') }}';
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
