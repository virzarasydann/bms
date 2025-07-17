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
                        <h1 class="text-xl mb-3">Data Project</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data Project</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Project</button>
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
                                            <th>Nama Project</th>
                                            <th>Customer</th>
                                            <th>Tanggal Kontrak</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Penanggung Jawab</th>
                                            <th>Status Pembayaran</th>
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
                            <label for="tgl_kontrak" class="col-sm-4 col-form-label">Tanggal Kontrak</label>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_kontrak" class="form-control" name="tgl_kontrak"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nama_project" class="col-sm-4 col-form-label">Nama Project</label>
                            <div class="col-sm-8">
                                <input type="text" id="nama_project" class="form-control" name="nama_project">
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="id_kategori_project" class="col-sm-4 col-form-label">Kategori Project</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="id_kategori_project" name="id_kategori_project">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($dataKategori as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <div class="form-group row mb-3">
                            <label for="id_customer" class="col-sm-4 col-form-label">Customer</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="id_customer" name="id_customer">
                                    <option value="">-- Pilih Customer --</option>
                                    @foreach ($dataCustomer as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nilai_project" class="col-sm-4 col-form-label">Nilai Project</label>
                            <div class="col-sm-8">
                                <input type="text" id="nilai_project" class="form-control" name="nilai_project">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="penanggung_jawab" class="col-sm-4 col-form-label">Penanggung Jawab</label>
                            <div class="col-sm-8">
                                <input type="text" id="penanggung_jawab" class="form-control" name="penanggung_jawab">
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
                            <label for="status_pembayaran" class="col-sm-4 col-form-label">Status Pembayaran</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status_pembayaran" name="status_pembayaran">


                                    <option value="Paid">Paid</option>
                                    <option value="Cicil">Cicil</option>
                                    <option value="DP">DP</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="tanggal_selesai" class="col-sm-4 col-form-label">Tanggal Selesai</label>
                            <div class="col-sm-3">
                                <input type="date" id="tanggal_selesai" class="form-control" name="tanggal_selesai"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <!-- tempat tetap untuk DP -->
                        <div id="wrapper-nominal-dp-container" class="form-group row mb-3"></div>


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
            store: "{{ route('project.store') }}",
            update: "{{ route('project.update', ['project' => ':id']) }}",
            index: "{{ route('project.index') }}"
        };
    </script>
    <script src="{{ asset('js/admin/project.js') }}"></script>
@endpush
