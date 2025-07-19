@extends('admin.layout_admin')

@section('content')
    <div class="content-wrapper">
        <div class="container d-flex justify-content-center">
            <div class="card w-75 shadow-lg border-0 mt-4 ml-5">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">Form Data</h5>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id" value="{{ $helpdesk->id }}">
                    <div class="modal-body">

                        <div class="form-group row mb-3">
                            <label for="tgl_komplen" class="col-sm-4 col-form-label">Tanggal Komplain</label>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_komplen" class="form-control" name="tgl_komplen"
                                    value="{{ $helpdesk->tgl_komplen }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_project" class="col-sm-4 col-form-label">Project</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="id_project" name="id_project">
                                    <option value="">-- Pilih Project --</option>
                                    @foreach ($dataProject as $project)
                                        <option value="{{ $project->id }}"
                                            {{ $project->id == $helpdesk->id_project ? 'selected' : '' }}>
                                            {{ $project->nama_project }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Deskripsi Komplain</label>
                            <div class="col-sm-8">
                                <div id="todo-wrapper">
                                    @if (is_array($helpdesk->deskripsi_komplen) && count($helpdesk->deskripsi_komplen) > 0)
                                        @foreach ($helpdesk->deskripsi_komplen as $index => $deskripsi)
                                            <div class="input-group mb-2">
                                                <input type="text" name="deskripsi_komplen[]" class="form-control"
                                                    value="{{ $deskripsi }}" placeholder="Masukkan komplain">
                                                <div class="input-group-append">
                                                    @if ($index === 0)
                                                        <button type="button" class="btn btn-success btn-sm add-field"><i
                                                                class="fas fa-plus"></i></button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-sm remove-field"><i
                                                                class="fas fa-trash"></i></button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" name="deskripsi_komplen[]" class="form-control"
                                                placeholder="Masukkan komplain">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success btn-sm add-field"><i
                                                        class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="penanggung_jawab" class="col-sm-4 col-form-label">Penanggung Jawab</label>
                            <div class="col-sm-8">
                                <input type="text" id="penanggung_jawab" class="form-control" name="penanggung_jawab"
                                    value="{{ $helpdesk->penanggung_jawab }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="status_komplen" class="col-sm-4 col-form-label">Status Komplain</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status_komplen" name="status_komplen">
                                    <option value="open" {{ $helpdesk->status_komplen == 'open' ? 'selected' : '' }}>Open
                                    </option>
                                    <option value="progress"
                                        {{ $helpdesk->status_komplen == 'progress' ? 'selected' : '' }}>Progress</option>
                                    <option value="closed" {{ $helpdesk->status_komplen == 'closed' ? 'selected' : '' }}>
                                        Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="catatan_penanggung_jawab" class="col-sm-4 col-form-label">Catatan Penanggung
                                Jawab</label>
                            <div class="col-sm-8">
                                <input type="text" id="catatan_penanggung_jawab" class="form-control"
                                    name="catatan_penanggung_jawab" value="{{ $helpdesk->catatan_penanggung_jawab }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="tgl_target_selesai" class="col-sm-4 col-form-label">Tanggal Target Selesai</label>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_target_selesai" class="form-control" name="tgl_target_selesai"
                                    value="{{ $helpdesk->tgl_target_selesai }}">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submitBtn" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('helpdesk.index') }}" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const wrapper = document.getElementById('todo-wrapper');

            wrapper.addEventListener('click', function(e) {
                if (e.target.closest('.add-field')) {
                    const newField = document.createElement('div');
                    newField.className = 'input-group mb-2';
                    newField.innerHTML = `
                    <input type="text" name="deskripsi_komplen[]" class="form-control" placeholder="Masukkan komplain">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-field"><i class="fas fa-trash"></i></button>
                    </div>
                `;
                    wrapper.appendChild(newField);
                }

                if (e.target.closest('.remove-field')) {
                    e.target.closest('.input-group').remove();
                }
            });
        });
    </script>
    <script>
        window.routes = {
            
            update: "{{ route('helpdesk.update', ['helpdesk' => ':id']) }}",
            
        };
    </script>
    <script src="{{ asset('js/admin/helpDesk.js') }}"></script>
@endpush
