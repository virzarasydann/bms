@extends('admin.layout_admin')

@section('content')
    <div class="content-wrapper">
        <div class="container d-flex justify-content-center">
            <div class="card w-75 shadow-lg border-0 mt-4 ml-5">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">Detail Project: {{ $project->nama_project }}</h5>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <div class="card-body">

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
                                <input type="text" class="form-control" value="{{ $project->penanggung_jawab }}"
                                    readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Nilai Project</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control"
                                    value="Rp {{ number_format($project->nilai_project, 0, ',', '.') }}" readonly>
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
@endsection
