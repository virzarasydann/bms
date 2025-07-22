class HelpDeskModule {
    constructor() {
        this.table = null;
        this.audio = new Audio('/audio/notification.ogg');
        this.permissions = window.permissions || {};
        this.init();
    }

    init() {
        this.initDataTable();
        this.initSelect2();
        this.handleFormSubmit();
        this.handleEdit();
        this.handleDelete();
        this.initAddField();
        this.resetModalOnClose();
    }

    initSelect2() {
        $('#id_project').select2({
            theme: "bootstrap4",
            placeholder: 'Pilih Project',
            dropdownParent: $('#modalForm')
        });

        $('#status_komplen').select2({
            theme: "bootstrap4",
            placeholder: 'Pilih Status Komplain',
            allowClear: false,
            minimumResultsForSearch: Infinity,
            dropdownParent: $('#modalForm')
        });

    }


    initDataTable() {
        this.table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ordering: false,
            ajax: window.routes.index,
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'project', name: 'project.nama_project' },
                { data: 'tgl_komplen' },
                { data: 'tgl_target_selesai' },
                { data: 'komplain' },
                { data: 'penanggung_jawab' },
                { data: 'status_komplen' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    }


    reloadTable() {
        this.table.ajax.reload();
    }

    initAddField() {
    const wrapper = document.getElementById('todo-wrapper');
    if (!wrapper) return;

    wrapper.addEventListener('click', function (e) {
        if (e.target.closest('.add-field')) {
            const newField = document.createElement('div');
            newField.className = 'row mb-2 align-items-center';
            newField.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="komplain[]" class="form-control" placeholder="Masukkan komplain">
                </div>
                <div class="col-md-5">
                    <input type="text" name="catatan_komplain[]" class="form-control" placeholder="Catatan komplain">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-field"><i class="fas fa-trash"></i></button>
                </div>
            `;
            wrapper.appendChild(newField);
        }

        if (e.target.closest('.remove-field')) {
            e.target.closest('.row').remove();
        }
    });
}

    handleFormSubmit() {

        $('#formData').on('submit', (e) => {
            const self = this;
            e.preventDefault();
            const id = $('#primary_id').val();
            const url = id ? window.routes.update.replace(':id', id) : window.routes.store;
            const method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            const formData = new FormData(e.target);
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: () => {
                    $('#modalForm').modal('hide');
                    this.audio.play();
                    toastr.success("Data telah disimpan!", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    self.reloadTable();
                },
                error: (xhr) => {
                    if (xhr.status === 422) {
                        this.audio.play();
                        toastr.error("Ada inputan yang salah!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, val) {
                            const input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(`<span class="invalid-feedback" role="alert"><strong>${val[0]}</strong></span>`);
                        });
                    }
                }
            });
        });
    }

   handleEdit() {
            $(document).on('click', '#edit-button', function () {
                const url = $(this).data('url');
                $.get(url, function (res) {
                    if (res.status === 'success') {
                        const data = res.data;

                        $('#primary_id').val(data.id);
                        $('#id_project').val(data.id_project).trigger('change');
                        $('#tgl_komplen').val(data.tgl_komplen);
                        $('#tgl_target_selesai').val(data.tgl_target_selesai);
                        $('#penanggung_jawab').val(data.penanggung_jawab);
                        $('#status_komplen').val(data.status_komplen).trigger('change');
                        $('#deskripsi').val(data.deskripsi);

                        $('#todo-wrapper').html('');

                        const komplainList = data.komplain || [];
                        const catatanList = data.catatan_komplain || [];

                        for (let i = 0; i < komplainList.length; i++) {
                            const komplain = komplainList[i] || '';
                            const catatan = catatanList[i] || '';

                            const field = `
                                <div class="row mb-2 align-items-center todo-item">
                                    <div class="col-md-5">
                                        <input type="text" name="komplain[]" class="form-control" value="${komplain}" placeholder="Masukkan komplain">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="catatan_komplain[]" class="form-control" value="${catatan}" placeholder="Catatan komplain">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-field"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>`;

                            $('#todo-wrapper').append(field);
                        }
                    }
                });
            });

            $(document).on('click', '.remove-field', function () {
                $(this).closest('.todo-item').remove();
            });
        }


    handleDelete() {
        const self = this;

        $(document).on('click', '.delete-button', function (e) {
            e.preventDefault();
            const form = $(this).closest('form');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function () {
                            toastr.success("Data telah dihapus!", "BERHASIL", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                            self.reloadTable();
                        },
                        error: function () {
                            toastr.error("Gagal menghapus data.", "GAGAL!", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                        }
                    });
                }
            });
        });
    }

resetModalOnClose() {
    $('#modalForm').on('hidden.bs.modal', function () {
        $('#formData')[0].reset();
        $('#primary_id').val('');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('.select2').val('').trigger('change');

        $('#todo-wrapper').html('');

        const createField = `
            <div class="row mb-2 align-items-center todo-item">
                <div class="col-md-5">
                    <input type="text" name="komplain[]" class="form-control" placeholder="Masukkan komplain">
                </div>
                <div class="col-md-5">
                    <input type="text" name="catatan_komplain[]" class="form-control" placeholder="Catatan komplain">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-sm add-field">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>`;
        $('#todo-wrapper').append(createField);
    });
}


}

$(document).ready(() => {
    window.HelpDeskApp = new HelpDeskModule();
});
