@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid"></div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-xl mb-3">Survey Pengajuan</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary btn-sm">
                                            Kembali
                                        </a>

                                        @if (isset($survey))
                                            <a class="btn btn-danger btn-sm"
                                                href="{{ route('cetak-survey', $survey->id) }}">
                                                <i class="fas fa-file-pdf"></i> Cetak PDF
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="showSurveyAlert()">
                                                <i class="fas fa-file-pdf"></i> Cetak PDF
                                            </button>
                                        @endif

                                        </a>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('pengajuan.storeSurvey', $data->id) }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <input type="hidden" name="id_pengajuan" value="{{ $data->id }}">

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Tanggal Survey</label>
                                        <div class="col-sm-3">
                                            <input type="date" id="tgl_survey" name="tgl_survey" class="form-control"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label for="no_survey" class="col-sm-2 col-form-label">No Survey</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="no_survey" class="form-control" name="no_survey"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Nama Lengkap</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="nama_lengkap" class="form-control"
                                                value="{{ $survey->nama_lengkap ?? $data->nama_lengkap }}">
                                        </div>

                                        <label class="col-sm-2 col-form-label">NIK</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="nik" class="form-control"
                                                value="{{ $survey->nik ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Alamat</label>
                                        <div class="col-sm-3">
                                            <textarea name="alamat" class="form-control" rows="2">{{ $survey->alamat ?? $data->alamat }}</textarea>
                                        </div>

                                        <label class="col-sm-2 col-form-label">No HP</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="no_hp" class="form-control"
                                                value="{{ $survey->no_telp ?? $data->no_telp }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Tempat Lahir</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="tempat_lahir" class="form-control"
                                                value="{{ $survey->tempat_lahir ?? '' }}">
                                        </div>

                                        <label class="col-sm-2 col-form-label">Tanggal Lahir</label>
                                        <div class="col-sm-3">
                                            <input type="date" name="tgl_lahir" class="form-control"
                                                value="{{ $survey->tgl_lahir ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Usia</label>
                                        <div class="col-sm-3">
                                            <input type="number" name="usia" class="form-control"
                                                value="{{ $survey->usia ?? '' }}">
                                        </div>

                                        <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                                        <div class="col-sm-3">
                                            <select name="jenis_kelamin" class="form-control select-jk">
                                                <option value="">Pilih</option>
                                                <option value="Laki-laki"
                                                    {{ isset($survey) && $survey->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="Perempuan"
                                                    {{ isset($survey) && $survey->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Status</label>
                                        <div class="col-sm-3">
                                            <select name="status" class="form-control select-stt">
                                                <option value=""></option>
                                                <option value="Belum"
                                                    {{ isset($survey) && $survey->status == 'Belum' ? 'selected' : '' }}>
                                                    Belum</option>
                                                <option value="Kawin"
                                                    {{ isset($survey) && $survey->status == 'Kawin' ? 'selected' : '' }}>
                                                    Kawin</option>
                                                <option value="Cerai"
                                                    {{ isset($survey) && $survey->status == 'Cerai' ? 'selected' : '' }}>
                                                    Cerai</option>
                                                <option value="Janda/Duda"
                                                    {{ isset($survey) && $survey->status == 'Janda/Duda' ? 'selected' : '' }}>
                                                    Janda/Duda</option>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Pekerjaan</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="pekerjaan" class="form-control"
                                                value="{{ $survey->pekerjaan ?? '' }}">
                                        </div>

                                        <label class="col-sm-2 col-form-label">Penghasilan</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="penghasilan" class="form-control"
                                                value="{{ $survey->penghasilan ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Lama Tinggal ditempat Sekarang</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="lama_tinggal" class="form-control"
                                                value="{{ $survey->lama_tinggal ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Status Tempat Tinggal</label>
                                        <div class="col-sm-3">
                                            <select name="stt_tempat_tinggal" class="form-control select-tempat">
                                                <option value=""></option>
                                                @foreach (['Rumah sendiri', 'Ngontrak', 'Numpang', 'Kos', 'Lainnya'] as $status)
                                                    <option value="{{ $status }}"
                                                        {{ isset($survey) && $survey->stt_tempat_tinggal == $status ? 'selected' : '' }}>
                                                        {{ $status }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Lembaga/orang yang pernah membantu</label>
                                        <div class="col-sm-3">
                                            <select name="membantu" id="membantu" class="form-control select-bantuan">
                                                <option value="">Pilih</option>
                                                <option value="Ada"
                                                    {{ isset($survey) && $survey->membantu == 'Ada' ? 'selected' : '' }}>
                                                    Ada</option>
                                                <option value="Tidak ada"
                                                    {{ isset($survey) && $survey->membantu == 'Tidak ada' ? 'selected' : '' }}>
                                                    Tidak ada</option>
                                            </select>
                                        </div>

                                        <label
                                            class="col-sm-2 col-form-label {{ isset($survey) && $survey->membantu == 'Ada' ? '' : 'd-none' }}"
                                            id="labelLembaga">Nama Lembaga/Orang</label>
                                        <div class="col-sm-4 {{ isset($survey) && $survey->membantu == 'Ada' ? '' : 'd-none' }}"
                                            id="inputLembagaWrapper">
                                            <input type="text" name="nama_lembaga_membantu" class="form-control"
                                                placeholder="Masukkan nama lembaga/orang"
                                                value="{{ $survey->nama_lembaga_membantu ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Orang terdekat yang bisa dihubungi</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="orang_terdekat" class="form-control"
                                                value="{{ $survey->orang_terdekat ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Masalah yang dihadapi</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <textarea name="masalah" class="form-control">{{ $survey->masalah ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Jumlah Tanggungan</label>
                                        <div class="col-sm-3">
                                            <input type="number" name="jumlah_tanggungan" class="form-control"
                                                value="{{ $survey->jumlah_tanggungan ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Upaya yang sudah dilakukan</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="usaha_dilakukan" class="form-control"
                                                value="{{ $survey->usaha_dilakukan ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Pengeluaran per bulan</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="pengeluaran_bulan" class="form-control"
                                                value="{{ $survey->pengeluaran_bulan ?? '' }}">
                                        </div>

                                        <label class="col-sm-2 col-form-label">Tabungan dimiliki</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="tabungan" class="form-control"
                                                value="{{ $survey->tabungan ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Hutang</label>
                                        <div class="col-sm-3">
                                            <select name="hutang" id="hutang" class="form-control select-hutang">
                                                <option value="">Pilih</option>
                                                <option value="Ada"
                                                    {{ isset($survey) && $survey->hutang == 'Ada' ? 'selected' : '' }}>
                                                    Ada</option>
                                                <option value="Tidak ada"
                                                    {{ isset($survey) && $survey->hutang == 'Tidak ada' ? 'selected' : '' }}>
                                                    Tidak ada</option>
                                            </select>
                                        </div>

                                        <label
                                            class="col-sm-2 col-form-label {{ isset($survey) && $survey->hutang == 'Ada' ? '' : 'd-none' }}"
                                            id="labelJumlahHutang">Jumlah Hutang</label>
                                        <div class="col-sm-4 {{ isset($survey) && $survey->hutang == 'Ada' ? '' : 'd-none' }}"
                                            id="inputJumlahHutangWrapper">
                                            <input type="text" name="jumlah_hutang" class="form-control"
                                                placeholder="Masukkan jumlah hutang (Rp)"
                                                value="{{ $survey->jumlah_hutang ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Bantuan yang diharapkan</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="harapan_bantuan" class="form-control"
                                                value="{{ $survey->harapan_bantuan ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Bersedia mengikuti kajian islam</label>
                                        <div class="col-sm-3">
                                            <select name="bersedia_kajian_islam" class="form-control select-sedia">
                                                <option value="">Pilih</option>
                                                <option value="Bersedia"
                                                    {{ isset($survey) && $survey->bersedia_kajian_islam == 'Bersedia' ? 'selected' : '' }}>
                                                    Bersedia</option>
                                                <option value="Tidak"
                                                    {{ isset($survey) && $survey->bersedia_kajian_islam == 'Tidak' ? 'selected' : '' }}>
                                                    Tidak</option>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success">Simpan Survey</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-jk').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih jenis kelamin",
            });
            $('.select-stt').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Status Perkawinan",
            });
            $('.select-tempat').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Status tempat tinggal",
            });
            $('.select-bantuan').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih",
            });
            $('.select-hutang').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih",
            });
            $('.select-sedia').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih",
            });

        });


        $(window).on('load', function() {
            const tanggal = $('#tgl_survey').val();
            if (tanggal) {
                fetchNomor(tanggal);
            } else {
                fetchNomor();
            }
        });

        function fetchNomor(tanggal = null) {
            $.ajax({
                url: "{{ route('survey.penomoran') }}",
                type: "GET",
                data: tanggal ? {
                    tgl_survey: tanggal
                } : {},
                success: function(response) {
                    if (response.status === 'success') {
                        $('#no_survey').val(response.data);
                    } else {
                        alert('Gagal mendapatkan nomor pengeluaran.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat memuat nomor pengeluaran.');
                }
            });
        }
        $(document).ready(function() {





            $('#membantu').on('change', function() {
                if ($(this).val() === 'Ada') {
                    $('#labelLembaga').removeClass('d-none');
                    $('#inputLembagaWrapper').removeClass('d-none');
                } else {
                    $('#labelLembaga').addClass('d-none');
                    $('#inputLembagaWrapper').addClass('d-none');
                    $('input[name="nama_lembaga_membantu"]').val('');
                }
            });

            $('#hutang').on('change', function() {
                if ($(this).val() === 'Ada') {
                    $('#labelJumlahHutang').removeClass('d-none');
                    $('#inputJumlahHutangWrapper').removeClass('d-none');
                } else {
                    $('#labelJumlahHutang').addClass('d-none');
                    $('#inputJumlahHutangWrapper').addClass('d-none');
                    $('input[name="jumlah_hutang"]').val('');
                }
            });

            $('#membantu').trigger('change');
            $('#hutang').trigger('change');
        });

        function showSurveyAlert() {
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Silakan isi data survey terlebih dahulu.',
                confirmButtonText: 'Oke, saya mengerti!'
            });
        }
    </script>
@endpush
