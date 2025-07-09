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
                        <h1 class="text-xl mb-3">Tutup Buku</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Tutup Buku</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus" id="tombol-tambah"></i>
                                                Tambah Tutup Buku</button>
                                        @endif



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
                            <label for="bulan" class="col-sm-4 col-form-label">Bulan</label>
                            <div class="col-sm-3">
                                @php
                                    $bulanSekarang = \Carbon\Carbon::now()->month;
                                    $bulanTutup = $bulanSekarang == 1 ? 12 : $bulanSekarang - 1;
                                    $namaBulan = \Carbon\Carbon::create()
                                        ->month($bulanTutup)
                                        ->locale('id')
                                        ->translatedFormat('F');
                                @endphp
                                <select id="bulan" name="bulan" class="form-control" required readonly>
                                    <option value="{{ $bulanTutup }}">{{ $namaBulan }}</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="tahun" class="col-sm-4 col-form-label">Tahun</label>
                            <div class="col-sm-3">
                                <input type="number" id="tahun" name="tahun" class="form-control"
                                    value="{{ $bulanSekarang == 1 ? now()->year - 1 : now()->year }}" readonly>
                            </div>
                        </div>







                    </div>

                    <div class="modal-footer" id="wrapper-footer">
                        <button type="submit" id="submitBtn" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    </div>
@endsection
@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');
        $(document).ready(function() {
            $('#formData').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: '{{ route('tutupBuku.store') }}',
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalForm').modal('hide');
                        audio.play();
                        toastr.success("Data telah disimpan!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        form[0].reset();
                        submitBtn.prop('disabled', false).text('Simpan');

                        // $('#tutupBukuTable').DataTable().ajax.reload(); // jika ada datatable
                    },
                    error: function(xhr) {
                        audio.play(); // typo sebelumnya: "udio"

                        let msg = "Ada inputan yang salah!";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        toastr.error(msg, "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        // Aktifkan kembali tombol submit
                        submitBtn.prop('disabled', false).text('Simpan');

                        // Tampilkan error validasi jika ada
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
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
        });
    </script>
@endpush
