@extends('admin.layout_admin')

@section('content')
    <div class="content-wrapper" style="min-height: 100vh;">
        <div class="container-fluid p-4">
            <div class="card border-0">
            <div class="card-header p-3">
              <div class="d-flex align-content-center justify-content-between">
                <h5 class="card-title mb-0">Detail Project: {{ $project->nama_project }}</h5>
               <div class="d-flex" style="gap: 5px">
                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalProgress">
                        Progress
                    </button>

                    <button type="button" class="btn border btn-success px-3 py-1 btn-xs" data-toggle="modal" data-target="#modalPembayaran">
                        Pembayaran
                    </button>
                </div>

                </div>
            </div>


                <form id="formData" class="h-100 d-flex flex-column">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div class="card-body flex-grow-1 overflow-auto">
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Nama Project</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $project->nama_project }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Tanggal Kontrak</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $project->tgl_kontrak }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Tanggal Selesai</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $project->tanggal_selesai }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Penanggung Jawab</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $project->penanggung_jawab }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Nilai Project</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="Rp {{ number_format($project->nilai_project, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Progress -->
    <div class="modal fade" id="modalProgress" tabindex="-1" aria-labelledby="modalProgressLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <form action="{{ route('progres.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="modalProgressLabel">Tambah Progress</h5>
                    </div>

                    <div class="modal-body">
                          <div class="form-group row mb-3">
                            <label for="tgl_progres" class="col-sm-4 col-form-label">Tanggal</label>
                            <div class="col-sm-3">
                            <input type="date" name="tgl_progres" id="tgl_progres" class="form-control"  value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                         <div class="form-group row mb-3">
                            <label for="stt_progres" class="col-sm-4 col-form-label">Status</label>
                            <div class="col-sm-3">
                                <select name="stt_progres" id="stt_progres" class="form-select select-stt" required>
                                    <option value=""></option>
                                    <option value="open">Open</option>
                                    <option value="progress">Progress</option>
                                    <option value="close">Close</option>
                                </select>
                            </div>
                        </div>

                           <div class="form-group row mb-3">
                            <label for="project_id" class="col-sm-4 col-form-label">Pilih Project</label>
                             <div class="col-sm-4">
                                <select name="project_id" id="project_id" class="form-control select-progres" required>
                                    <option value=""></option>
                                    @foreach ($projectsList as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->nama_project }} - {{ $project->customer->nama ?? 'Tanpa Customer' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                       <div class="form-group row mb-3">
                            <label for="catatan" class="col-sm-4 col-form-label form-label">Catatan</label>
                              <div class="col-sm-4">
                                 <textarea name="catatan" id="catatan"  class="form-control" placeholder="Masukan Catatan Anda" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran -->
    <div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel" aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <form action="{{ route('pembayaran.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="modalPembayaranLabel">Tambah Pembayaran</h5>
                    </div>

                    <div class="modal-body">
                        <div class="form-group row mb-3">
                            <label for="tgl_pembayaran" class="col-sm-4 col-form-label">Tanggal</label>
                            <div class="col-sm-3">
                            <input type="date" name="tgl_pembayaran" id="tgl_pembayaran" class="form-control"  value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_project" class="col-sm-4 col-form-label form-label">Pilih Project</label>
                             <div class="col-sm-4">
                                <select name="id_project" id="id_project" class="form-control select-ProjectPembayaran" required>
                                    <option value=""></option>
                                    @foreach ($projectsList as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->nama_project }} - {{ $project->customer->nama ?? 'Tanpa Customer' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nominal" class="col-sm-4 col-form-label form-label">Nominal</label>
                            <div class="col-sm-4">
                                <div id="nominal-wrapper">
                                    <div class="input-group mb-2">
                                        <input type="text" name="nominal[]" class="form-control" placeholder="Masukan Nominal" required>
                                        <button type="button" class="btn btn-success btn-add-nominal" style="margin-left:10px;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="catatan" class="col-sm-4 col-form-label form-label">Catatan</label>
                              <div class="col-sm-4">
                                 <textarea name="catatan" id="catatan"  class="form-control" placeholder="Masukan Catatan Anda" required></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script type="text/javascript">
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function () {
            @if (Session::has('success'))
                audio.play();
                toastr.success("{{ Session::get('success') }}", "Berhasil!", {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 3000,
                    positionClass: 'toast-bottom-right',
                });
            @endif

            @if ($errors->any())
                audio.play();
                toastr.error("{{ $errors->first() }}", "Kesalahan!", {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 3000,
                    positionClass: 'toast-bottom-right'
                });
            @endif
        });

        $(document).ready(function() {
            $('.select-progres').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Project",
            });
            $('.select-stt').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status",
                minimumResultsForSearch: Infinity,
            });
              $('.select-ProjectPembayaran').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Project",
            });
        });



          $(document).ready(function () {
            $(document).on('click', '.btn-add-nominal', function () {
                let newInput = `
                    <div class="input-group mb-2">
                        <input type="text" name="nominal[]" class="form-control" placeholder="Masukan Nominal" required>
                        <button type="button" class="btn btn-danger btn-remove-nominal" style="margin-left:10px;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>`;
                $('#nominal-wrapper').append(newInput);
            });

            $(document).on('click', '.btn-remove-nominal', function () {
                $(this).closest('.input-group').remove();
            });
        });

         function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        function handleRupiahInput(el) {
            el.addEventListener('input', function (e) {
                let cursorPosition = el.selectionStart;
                let unformatted = el.value.replace(/[^0-9]/g, '');
                el.value = formatRupiah(unformatted);
                el.setSelectionRange(cursorPosition, cursorPosition);
            });
        }

        function initRupiahInputs() {
            document.querySelectorAll('input[name="nominal[]"]').forEach((input) => {
                handleRupiahInput(input);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initRupiahInputs();

            document.addEventListener('click', function (e) {
                if (e.target.closest('.btn-add-nominal')) {
                    setTimeout(() => initRupiahInputs(), 100);
                }
            });
        });

    </script>
@endpush
