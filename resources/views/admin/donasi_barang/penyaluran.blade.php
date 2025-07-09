@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Penyaluran Donasi Barang</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                {{-- Detail Donasi Barang --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Donasi Barang</h3>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Donasi</label>
                                        <input type="text" class="form-control" value="{{ $donasi->tgl_donasi }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Barang</label>
                                        <input type="text" class="form-control" value="{{ $donasi->nama_barang }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jumlah</label>
                                        <input type="number" class="form-control" value="{{ $donasi->jumlah }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Satuan</label>
                                        <input type="text" class="form-control" value="{{ $donasi->satuan }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Donatur</label>
                                        <input type="text" class="form-control" value="{{ $donasi->nama_donatur }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $donasi->keterangan }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <div class="card-body">
                        <form id="formPenyaluran" action="{{ route('penyaluran.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="donasi_barang_id" value="{{ $donasi->id }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Penyaluran</label>
                                        <input type="date" class="form-control" name="tgl_penyaluran"
                                               value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jenis Penyaluran</label>
                                        <select class="form-control select-jenis" name="jenis_penyaluran" id="jenisPenyaluran" required>
                                            <option value="">Pilih Jenis</option>
                                            <option value="ke orang">Ke Orang</option>
                                            <option value="dijual">Dijual</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                              <div id="fieldPenerima" class="form-group row mb-3" style="display: none;">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="penerima_list">Penerima</label>
                                        <select class="form-control select2" id="penerima_list"
                                            name="penerima_list"></select>
                                        </div>
                                    </div>
                                </div>

                            <div id="fieldKeterangan" class="row" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div id="fieldTipe" class="row" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipe</label>
                                        <select class="form-control select-tipe" name="tipe">
                                            <option value="Bank">Bank</option>
                                            <option value="Cash">Cash</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                                <button type="submit" class="btn btn-success float-right">Simpan</button>
                        </form>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>

    $(document).ready(function() {
        $('.select-tipe').select2({
            theme: "bootstrap4",
            minimumResultsForSearch: Infinity,
            placeholder: "Pilih Tipe",
        });
        $('.select-jenis').select2({
            theme: "bootstrap4",
            minimumResultsForSearch: Infinity,
            placeholder: "Pilih Jenis Penyaluran",
        });
    });


$(document).ready(function() {
$('#penerima_list').select2({
        placeholder: 'Pilih Penerima',
        ajax: {
            url: '{{ route('penerimaan.list') }}',
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


    $('#jenisPenyaluran').change(function() {
        const jenis = $(this).val();

        $('#fieldPenerima, #fieldKeterangan, #fieldTipe').hide();

        if(jenis === 'ke orang') {
            $('#fieldPenerima, #fieldKeterangan').show();
            $('input[name=nama_penerima]').attr('required', true);
        } else if(jenis === 'dijual') {
            $('#fieldPenerima, #fieldTipe').show();
            $('input[name=nama_penerima]').attr('required', true);
            $('select[name=tipe]').attr('required', true);
        } else {
            $('input[name=nama_penerima]').removeAttr('required');
            $('select[name=tipe]').removeAttr('required');
        }
    });
});
</script>
@endpush
