class SewaModule {
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
        this.resetModal();
    }

    initSelect2() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih Kategori',
            allowClear: false,
            dropdownParent: $('#modalForm')
        });
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
                { data: 'kategori', name: 'kategori.jenis_sewa' },
                { data: 'nama_layanan' },
                { data: 'email' },
                { data: 'tgl_sewa' },
                { data: 'tgl_expired' },
                { data: 'vendor' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    }

    handleFormSubmit() {
        $('#formData').on('submit', (e) => {
            e.preventDefault();

            const id = $('#primary_id').val();
            const url = id ? window.routes.update.replace(':id', id) : window.routes.store;
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
                    this.table.ajax.reload();
                },
                error: (xhr) => {
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
            });
        });
    }

    handleEdit() {
        $(document).on('click', '#edit-button', function () {
            const url = $(this).data('url');

            $.get(url, (res) => {
                if (res.status === 'success') {
                    const d = res.data;
                    $('#primary_id').val(d.id);
                    $('#id_kategori_sewa').val(d.id_kategori_sewa).trigger('change');
                    $('#nama_layanan').val(d.nama_layanan);
                    $('#email').val(d.email);
                    $('#password').val(d.password);
                    $('#tgl_sewa').val(d.tgl_sewa);
                    $('#tgl_expired').val(d.tgl_expired);
                    $('#vendor').val(d.vendor);
                    $('#url_vendor').val(d.url_vendor);
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

    resetModal() {
        $('#modalForm').on('hidden.bs.modal', function () {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.select2').val('').trigger('change');
        });
    }
}

$(document).ready(() => {
    window.SewaApp = new SewaModule();
});
