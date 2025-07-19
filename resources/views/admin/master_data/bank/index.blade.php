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
                        <h1 class="text-xl mb-3">Data Bank</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data Bank</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Bank</button>
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
                                            <th>Nama Bank</th>
                                            <th>No Rekening</th>
                                            <th>Pemilik</th>

                                        
                                            
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
                            <label for="nama_bank" class="col-sm-4 col-form-label">Nama Bank</label>
                            <div class="col-sm-8">
                                <input type="text" id="nama_bank" class="form-control" name="nama_bank">
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="no_rekening" class="col-sm-4 col-form-label">No Rekening</label>
                            <div class="col-sm-8">
                                <input type="text" id="no_rekening" class="form-control" name="no_rekening">
                            </div>
                        </div>

                    

                        <div class="form-group row mb-3">
                            <label for="pemilik" class="col-sm-4 col-form-label">Pemilik</label>
                            <div class="col-sm-8">
                                <input type="text" id="pemilik" class="form-control"
                                    name="pemilik">
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
            index: "{{ route('bank.index') }}",
            store: "{{ route('bank.store') }}",
            update: "{{ route('bank.update', ['bank' => ':id']) }}",
        };
    </script>
    <script src="{{ asset('js/admin/bank.js') }}"></script>
@endpush
