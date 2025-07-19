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
                        <h1 class="text-xl mb-3">Data Hutang</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data Hutang</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Hutang</button>
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
                                            <th>Tanggal Hutang</th>
                                            <th>Deskripsi</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                            <th>Lampiran</th>
                                            <th>Tanggal Pelunasan</th>




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
                            <label for="tanggal_hutang" class="col-sm-4 col-form-label">Tanggal Hutang</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="tanggal_hutang" name="tanggal_hutang"
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
                            <label for="deskripsi" class="col-sm-4 col-form-label">Deskripsi</label>
                            <div class="col-sm-8">
                                <input type="text" id="deskripsi" class="form-control" name="deskripsi">
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
        window.permissions = @json($permissions);
        window.routes = {
            index: "{{ route('hutang.index') }}",
            store: "{{ route('hutang.store') }}",
            update: "{{ route('hutang.update', ['hutang' => ':id']) }}",
        };
    </script>
    <script src="{{ asset('js/admin/hutang.js') }}"></script>
@endpush
