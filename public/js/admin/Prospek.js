class ProspekModule {
    constructor(permissions, urls) {
        this.permissions = permissions;
        this.urls = urls;
        this.audio = new Audio('/audio/notification.ogg');
        this.table = null;
        this.form = $('#formData');
        this.modal = $('#modalForm');
        this.submitBtn = $('#submitBtn');

        this.initDataTable();
        this.bindEvents();
    }

    initDataTable() {
        const showActionColumn = this.permissions.edit || this.permissions.hapus;

        this.table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            responsive: true,
            ajax: this.urls.index,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                { data: 'nama', name: 'nama' },
                { data: 'alamat', name: 'alamat' },
                { data: 'no_telp', name: 'no_telp' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    visible: showActionColumn
                }
            ]
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
        const url = isUpdate ? this.urls.update.replace('__id__', id) : this.urls.store;
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
                toastr.success("Data berhasil disimpan!", "Sukses");
                this.modal.modal('hide');
                this.table.ajax.reload(null, false);
                this.clearForm();
            },
            error: xhr => {
                this.audio.play();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, (key, val) => {
                        const input = $('#' + key);
                        input.addClass('is-invalid');
                        input.parent().append(
                            `<span class="invalid-feedback" role="alert"><strong>${val[0]}</strong></span>`
                        );
                    });
                } else {
                    toastr.error("Gagal menyimpan data!", "Error");
                }
            }
        });
    }

    handleEdit(e) {
        const url = $(e.currentTarget).data('url');

        $.get(url, res => {
            if (res.status === 'success') {
                const data = res.data;
                $('#primary_id').val(data.id);
                $('#nama').val(data.nama);
                $('#alamat').val(data.alamat);
                $('#no_telp').val(data.no_telp);
                this.modal.modal('show');
            }
        });
    }

    handleDelete(e) {
        e.preventDefault();
        const form = $(e.currentTarget).closest('form');

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(form.attr('action'), form.serialize(), () => {
                    this.audio.play();
                    toastr.success("Data berhasil dihapus!", "Sukses");
                    this.table.ajax.reload(null, false);
                }).fail(() => {
                    this.audio.play();
                    toastr.error("Gagal menghapus data!", "Error");
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
        index: window.prospekIndexUrl,
        store: window.prospekrStoreUrl,
        update: window.prospekUpdateUrl
    };
    new ProspekModule(permissions, urls);
});
