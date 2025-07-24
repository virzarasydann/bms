@extends('admin.layout_admin')

@section('content')
    <div class="content-wrapper" style="min-height: 100vh;">
        <div class="container-fluid p-4">
            <div class="card border-0">
            <div class="card-header p-3">
              <div class="d-flex align-content-center justify-content-between">
                <h5 class="card-title mb-0">Detail Project: {{ $project->nama_project }}</h5>
                <div class="d-flex" style="gap: 5px">
                        <button type="button" class="btn btn-sm btn-info me-2" data-toggle="modal"
                            onclick="#" data-target="#modalTambah">
                            Progress
                        </button>
                        <button type="button" class="btn border btn-success px-3 py-1 btn-xs" onclick="#">
                            Pembayaran
                        </button>
                </div>
                </div>
            </div>


                <form id="formData" class="h-100 d-flex flex-column">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div class="card-body flex-grow-1 overflow-auto">
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
                                <input type="text" class="form-control" value="{{ $project->penanggung_jawab }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Nilai Project</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="Rp {{ number_format($project->nilai_project, 0, ',', '.') }}" readonly>
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
