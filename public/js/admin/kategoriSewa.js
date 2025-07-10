class KategoriSewaModule {
    constructor(permissions, urls) {
        this.permissions = permissions;
        this.urls = urls;
        this.audio = new Audio('/audio/notification.ogg');
        this.form = $('#formData');
        this.modal = $('#modalForm');
        this.submitBtn = $('#submitBtn');
        this.table = null;

        this.initTable();
        this.bindEvents();
    }

    initTable() {
        const showAction = this.permissions.edit || this.permissions.hapus;

        this.table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            responsive: true,
            ajax: this.urls.index,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenis_sewa', name: 'jenis_sewa' },
                { data: 'keterangan', name: 'keterangan' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    visible: showAction
                }
            ],
            columnDefs: [{
                targets: 0,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }]
        });
    }

    bindEvents() {
        this.form.on('submit', this.handleSubmit.bind(this));
        $(document).on('click', '.edit-button', this.handleEdit.bind(this));
        $(document).on('click', '.delete-button', this.handleDelete.bind(this));
        this.modal.on('hidden.bs.modal', this.clearForm.bind(this));
        window.reloadTable = () => this.table.ajax.reload();
    }

    handleSubmit(e) {
        e.preventDefault();

        const id = $('#primary_id').val();
        const isUpdate = !!id;
        const url = isUpdate ? this.urls.update.replace(':id', id) : this.urls.store;
        const method = isUpdate ? 'PUT' : 'POST';

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        const formData = new FormData(this.form[0]);
        formData.append('_method', method);

        

        $.ajax({
            url,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: () => {
                this.audio.play();
                toastr.success("Data telah disimpan!", "BERHASIL", {
                    progressBar: true,
                    timeOut: 3500,
                    positionClass: "toast-bottom-right",
                });
                this.modal.modal('hide');
                this.table.ajax.reload(null, false);
                this.clearForm();
            },
            error: xhr => {
                this.audio.play();
                if (xhr.status === 422) {
                    toastr.error("Ada inputan yang salah!", "GAGAL!", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });

                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, val) {
                        let input = $('#' + key);
                        input.addClass('is-invalid');
                        input.parent().find('.invalid-feedback').remove();
                        input.parent().append(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                            val[0] + '</strong></span>'
                        );
                    });
                } else {
                    toastr.error("Gagal menyimpan data!", "GAGAL!", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                }
            }
        });
    }

    handleEdit(e) {
        const url = $(e.currentTarget).data('url');

        $.get(url, res => {
            if (res.status === 'success') {
                $('#primary_id').val(res.data.id);
                $('#jenis_sewa').val(res.data.jenis_sewa);
                $('#keterangan').val(res.data.keterangan);
                this.modal.modal('show');
            }
        });
    }

    handleDelete(e) {
        e.preventDefault();
        const form = $(e.currentTarget).closest('form');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data ini akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: () => {
                        this.audio.play();
                        toastr.success("Data telah dihapus!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                        this.table.ajax.reload(null, false);
                    },
                    error: () => {
                        this.audio.play();
                        toastr.error("Gagal menghapus data.", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                    }
                });
            }
        });
    }

    clearForm() {
        this.form[0].reset();
        $('#primary_id').val('');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }
}

$(function () {
    const permissions = window.appPermissions || {};
    const urls = {
        index: window.kategoriSewaIndexUrl,
        store: window.kategoriSewaStoreUrl,
        update: window.kategoriSewaUpdateUrl,
    };

    new KategoriSewaModule(permissions, urls);
});
