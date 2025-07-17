class DataAkunModule {
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
        this.resetModalOnClose();
    }

    initDataTable() {
        this.table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: window.routes.index,
            ordering: false,
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_akun' },
                { data: 'user_id' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    }

    reloadTable() {
        this.table.ajax.reload();
    }

    handleFormSubmit() {
        $('#formData').on('submit', (e) => {
            e.preventDefault();
            const id = $('#primary_id').val();
            const url = id
                ? window.routes.update.replace(':id', id)
                : window.routes.store;
            const method = id ? 'PUT' : 'POST';

            const formData = new FormData(e.target);
            formData.append('_method', method);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: () => {
                    $('#modalForm').modal('hide');
                    this.audio.play();
                    toastr.success("Data akun berhasil disimpan!", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    this.reloadTable();
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
                        $.each(errors, (key, val) => {
                            const input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(
                                `<span class="invalid-feedback" role="alert"><strong>${val[0]}</strong></span>`
                            );
                        });
                    }
                }
            });
        });
    }

    handleEdit() {
        const self = this;
        $(document).on('click', '.edit-button', function () {
            const url = $(this).data('url');

            $.get(url, function (res) {
                if (res.status === 'success') {
                    const data = res.data;

                    $('#primary_id').val(data.id);
                    $('#nama_akun').val(data.nama_akun);
                    $('#user_id').val(data.user_id);
                    $('#password').val(''); // Kosongkan karena tidak perlu tampil password lama

                    $('#modalForm').modal('show');
                }
            });
        });
    }

    handleDelete() {
        $(document).on('click', '.delete-button', (e) => {
            e.preventDefault();
            const form = $(e.target).closest('form');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: () => {
                            toastr.success("Data akun berhasil dihapus!", "BERHASIL", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                            this.table.ajax.reload();
                        },
                        error: () => {
                            toastr.error("Gagal menghapus data akun.", "GAGAL!", {
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
        $('#modalForm').on('hidden.bs.modal', () => {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.select2').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#formData :input').prop('disabled', false);
        });
    }

    initSelect2() {
        $('.select2').select2({
            theme: "bootstrap4",
            placeholder: '-- Pilih --',
            allowClear: false,
            dropdownParent: $('#modalForm')
        });
    }
}

$(function () {
    window.DataAkunApp = new DataAkunModule();
});
