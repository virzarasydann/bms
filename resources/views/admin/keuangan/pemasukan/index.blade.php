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
                                    <h3 class="font-weight-medium text-lg">Data Pemasukan</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Pemasukan</button>
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
                                            <th width="5%">No</th>
                                            <th>Tanggal</th>
                                            <th>Nominal</th>
                                            <th>Kategori</th>
                                            <th>Rekening</th>
                                            <th>Keterangan</th>
                                            <th width="50px">Lampiran</th>
                                        

                                        
                                            
                                            <th width="100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <div class="modal fade" id="modalLampiran" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Preview Lampiran</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
                                            </div>
                                            <div class="modal-body" id="preview-lampiran-body"></div>
                                        </div>
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
                            <label for="tanggal" class="col-sm-4 col-form-label">Tanggal Pemasukan</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="id_bank" class="col-sm-4 col-form-label">Rekening</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="id_bank" name="id_bank">
                                    <option value="">-- Pilih Rekening --</option>
                                    @foreach ($dataBank as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->nama_bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        
                    
                        <div class="form-group row mb-3">
                            <label for="nominal" class="col-sm-4 col-form-label">Nominal</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control" id="nominal" name="nominal"
                                        oninput="formatRupiah(this)">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="lampiran" class="col-sm-4 col-form-label">Lampiran</label>
                            <div class="col-sm-8">
                                <input type="file" class="form-control" id="lampiran" name="lampiran"
                                    accept=".jpg,.jpeg,.png,.pdf">
                                <div class="d-flex gap-1 mt-1 align-items-center small">
                                    <div id="lihat-lampiran-wrapper" class="d-none">
                                        <a href="#" target="_blank" id="lihat-lampiran" class="text-primary">Lihat
                                            Lampiran</a>
                                        <span class="text-dark">|</span>
                                    </div>
                                    <small class="text-danger">Max. 2MB (.jpg, .png, .jpeg, .pdf)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="keterangan" class="col-sm-4 col-form-label">Keterangan</label>
                            <div class="col-sm-8">
                                <input type="text" id="keterangan" class="form-control" name="keterangan">
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="id_kategori_transaksi" class="col-sm-4 col-form-label">Kategori Transaksi</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="id_kategori_transaksi" name="id_kategori_transaksi">
                                    <option value="">-- Pilih Rekening --</option>
                                    @foreach ($dataKategori as $kategori)
                                        <option value="{{ $kategori->id }}" data-nama="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>

                            

                        </div>

                        <div class="form-group row mb-3" id="wrapper-select-piutang" style="display: none;">
                            <label for="id_piutang" class="col-sm-4 col-form-label">Pilih Piutang</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="id_piutang" name="id_piutang">
                                    <option value="">-- Pilih Piutang --</option>
                                    @foreach ($dataPiutang as $piutang)
                                        <option value="{{ $piutang->id }}" data-nama="{{ $piutang->project->nama_project ?? '-' }}">
                                            Piutang dari project {{ $piutang->project->nama_project ?? '-' }}
                                        </option>
                                    @endforeach
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
        // window.dataPiutang = @json($dataPiutang);
        window.permissions = @json($permissions);
        window.routes = {
            index: "{{ route('pemasukan.index') }}",
            store: "{{ route('pemasukan.store') }}",
            update: "{{ route('pemasukan.update', ['pemasukan' => ':id']) }}",
        };
    </script>
    <script src="{{ asset('js/admin/pemasukan.js') }}"></script>
@endpush
