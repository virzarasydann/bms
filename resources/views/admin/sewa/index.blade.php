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
                        <h1 class="text-xl mb-3">Data Sewa</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data Sewa</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Sewa</button>
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
                                            <th>Kategori Sewa</th>
                                            <th>Nama Layanan</th>
                                            <th>Email</th>

                                            <th>Tanggal Sewa</th>
                                            <th>Tanggal Expired</th>
                                            <th>Vendor</th>
                                            <th width="50px">Action</th>
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
                <div class="modal-header bg-warning">
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
                            <label for="tgl_sewa" class="col-sm-4 col-form-label">Tanggal Sewa</label>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_sewa" class="form-control" name="tgl_sewa"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nama_layanan" class="col-sm-4 col-form-label">Nama Layanan</label>
                            <div class="col-sm-8">
                                <input type="text" id="nama_layanan" class="form-control" name="nama_layanan">
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="email" class="col-sm-4 col-form-label">Email</label>
                            <div class="col-sm-8">
                                <input type="text" id="email" class="form-control" name="email">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="password" class="col-sm-4 col-form-label">Password</label>
                            <div class="col-sm-8">
                                <input type="text" id="password" class="form-control" name="password">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_kategori_sewa" class="col-sm-4 col-form-label">Kategori Sewa</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="id_kategori_sewa" name="id_kategori_sewa">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($dataKategori as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->jenis_sewa }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="vendor" class="col-sm-4 col-form-label">Vendor</label>
                            <div class="col-sm-8">
                                <input type="text" id="vendor" class="form-control" name="vendor">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="url_vendor" class="col-sm-4 col-form-label">URL Vendor</label>
                            <div class="col-sm-8">
                                <input type="text" id="url_vendor" class="form-control" name="url_vendor">
                            </div>
                        </div>




                        <div class="form-group row mb-3">
                            <label for="tgl_expired" class="col-sm-4 col-form-label">Tanggal Expired</label>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_expired" class="form-control" name="tgl_expired"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary">Simpan</button>
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
            index: "{{ route('sewa.index') }}",
            store: "{{ route('sewa.store') }}",
            update: "{{ route('sewa.update', ['sewa' => ':id']) }}",
        };
    </script>
    <script src="{{ asset('js/admin/sewa.js') }}"></script>
@endpush
