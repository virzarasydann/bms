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
                        <h1 class="text-xl mb-3">Data Help Desk</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data Help Desk</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Help Desk</button>
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
                                            <th>Project</th>
                                            <th>Tgl Komplain</th>
                                            <th>Tgl Target Selesai</th>

                                            <th>Deskripsi Komplain</th>
                                            <th>Penanggung Jawab</th>
                                            <th>Status Komplain</th>
                                            
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
                            <label for="tgl_komplen" class="col-sm-4 col-form-label">Tanggal Komplain</label>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_komplen" class="form-control" name="tgl_komplen"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_project" class="col-sm-4 col-form-label">Project</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="id_project" name="id_project">
                                    <option value="">-- Pilih Project --</option>
                                    @foreach ($dataProject as $project)
                                        <option value="{{ $project->id }}">{{ $project->nama_project }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="deskripsi_komplen" class="col-sm-4 col-form-label">Deskripsi Komplain</label>
                            <div class="col-sm-8">
                                <input type="text" id="deskripsi_komplen" class="form-control" name="deskripsi_komplen">
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="penanggung_jawab" class="col-sm-4 col-form-label">Penanggung Jawab</label>
                            <div class="col-sm-8">
                                <input type="text" id="penanggung_jawab" class="form-control" name="penanggung_jawab">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="status_komplen" class="col-sm-4 col-form-label">Status Komplain</label>
                            <div class="col-sm-8">
                                <select class="form-control " id="status_komplen" name="status_komplen">
                                    <option value="open">Open</option>
                                    <option value="progress">Progress</option>
                                    <option value="closed">Closed</option>


                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="catatan_penanggung_jawab" class="col-sm-4 col-form-label">Catatan Penanggung
                                Jawab</label>
                            <div class="col-sm-8">
                                <input type="text" id="catatan_penanggung_jawab" class="form-control"
                                    name="catatan_penanggung_jawab">
                            </div>
                        </div>








                        <div class="form-group row mb-3">
                            <label for="tgl_target_selesai" class="col-sm-4 col-form-label">Tanggal Target Selesai</label>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_target_selesai" class="form-control"
                                    name="tgl_target_selesai" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
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
            index: "{{ route('helpdesk.index') }}",
            store: "{{ route('helpdesk.store') }}",
            update: "{{ route('helpdesk.update', ['helpdesk' => ':id']) }}",
        };
    </script>
    <script src="{{ asset('js/admin/helpDesk.js') }}"></script>
@endpush
