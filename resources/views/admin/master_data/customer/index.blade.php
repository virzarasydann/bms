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
                        <h1 class="text-xl mb-3">Data Customer</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data Customer</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                           @if ($permissions['tambah'])
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalForm"><i
                                                class="fas fa-plus"></i>
                                            Tambah Customer</button>
                                          @endif

                                        <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                            Reload
                                        </button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Alamat</th>
                                            <th>No Telpon</th>
                                            <th>Email</th>
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
                <div class="modal-header bg-success">
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
                            <label for="nama" class="col-sm-4 col-form-label">Nama Customer</label>
                            <div class="col-sm-8">
                                <input type="text" id="nama" class="form-control" name="nama">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="alamat" class="col-sm-4 col-form-label">Alamat</label>
                            <div class="col-sm-8">
                                <input type="text" id="alamat" class="form-control" name="alamat">
                            </div>
                        </div>

                        
                        <div class="form-group row mb-3">
                            <label for="no_telp" class="col-sm-4 col-form-label">No Telpon</label>
                            <div class="col-sm-8">
                                <input type="text" id="no_telp" class="form-control" name="no_telp">
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="email" class="col-sm-4 col-form-label">Email</label>
                            <div class="col-sm-8">
                                <input type="text" id="email" class="form-control" name="email">
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
    window.customerIndexUrl = "{{ route('customer.index') }}";
    window.customerStoreUrl = "{{ route('customer.store') }}";
    window.customerUpdateUrl = "{{ route('customer.update', ':id') }}";
    window.appPermissions = @json($permissions);
</script>

<script src="{{ asset('js/admin/customer.js') }}"></script>
@endpush

