class KategoriTransaksiModule {
    constructor() {
        this.table = null;
        this.audio = new Audio('/audio/notification.ogg');
        this.permissions = window.permissions || {};
        this.init();
    }

    init() {
        this.initDataTable();
        this.handleFormSubmit();
        this.handleEdit();
        this.handleDelete();
        this.resetModalOnClose();
    }

    initDataTable() {
        const showAction = this.permissions.edit || this.permissions.hapus;
        this.table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: window.routes.index,
            ordering: false,
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_kategori' },
                { data: 'jenis_transaksi' },
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

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            const formData = new FormData(e.target);
            formData.append('_method', method);

            $.ajax({
                url,
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
                        $.each(errors, function (key, val) {
                            const input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
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
                    $('#nama_kategori').val(data.nama_kategori);
                    $('#jenis_transaksi').val(data.jenis_transaksi).trigger('change');
                }
            });
        });
    }

    handleDelete() {
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
                        success: () => {
                            toastr.success("Data telah dihapus!", "BERHASIL", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                            this.reloadTable();
                        },
                        error: () => {
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
        });
    }
}

$(document).ready(() => {
    window.KategoriTransaksiApp = new KategoriTransaksiModule();
});
