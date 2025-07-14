class MutasiSaldoModule {
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
        this.handlePreview();
    }

    initSelect2() {
        $('.select2').select2({
            theme: "bootstrap4",
            allowClear: false,
            dropdownParent: $('#modalForm')
        });
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
                { data: 'tanggal' },
                { data: 'asal' },
                { data: 'tujuan' },
                { data: 'nominal' },
                { data: 'lampiran' },
                { data: 'keterangan' },
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
                    toastr.success("Data berhasil disimpan!", "Sukses", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right"
                    });
                    this.reloadTable();
                },
                error: (xhr) => {
                    if (xhr.status === 422) {
                        this.audio.play();
                        toastr.error("Validasi gagal!", "Gagal", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right"
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
        $(document).on('click', '.edit-button', function () {
            const url = $(this).data('url');

            $.get(url, (res) => {
                if (res.status === 'success') {
                    const data = res.data;

                    $('#primary_id').val(data.id);
                    $('#tanggal').val(data.tanggal);
                    $('#rekening_asal').val(data.rekening_asal).trigger('change');
                    $('#rekening_tujuan').val(data.rekening_tujuan).trigger('change');
                    $('#nominal').val(data.nominal);
                    $('#keterangan').val(data.keterangan);

                    if (res.lampiran_url) {
                        $('#lihat-lampiran').attr('href', res.lampiran_url).text('Lihat Lampiran');
                        $('#lihat-lampiran-wrapper').removeClass('d-none');
                    } else {
                        $('#lihat-lampiran-wrapper').addClass('d-none');
                    }
                }
            });
        });
    }

    handleDelete() {
        $(document).on('click', '.delete-button', (e) => {
            e.preventDefault();
            const form = $(e.currentTarget).closest('form');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data akan dihapus secara permanen.',
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
                            toastr.success("Data berhasil dihapus!", "Sukses", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right"
                            });
                            this.reloadTable();
                        },
                        error: () => {
                            toastr.error("Gagal menghapus data!", "Error", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right"
                            });
                        }
                    });
                }
            });
        });
    }

    handlePreview() {
        $(document).on('click', '.btn-preview-lampiran', function (e) {
            e.preventDefault();
            const url = $(this).data('url');
            let html = '';
    
            if (url.match(/\.(jpg|jpeg|png)$/i)) {
                html = `<img src="${url}" class="img-fluid w-100 h-auto" alt="Lampiran">`;
            } else if (url.match(/\.(pdf)$/i)) {
                html = `<embed src="${url}" type="application/pdf" style="width:100%; height:80vh;" />`;
            } else {
                html = `<p class="text-danger">File tidak dapat ditampilkan.</p>`;
            }
    
            $('#preview-lampiran-body').html(html);
            $('#modalLampiran').modal('show');
        });
    }

    resetModalOnClose() {
        $('#modalForm').on('hidden.bs.modal', function () {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.select2').val('').trigger('change');
            $('#lihat-lampiran-wrapper').addClass('d-none');
            $('#lihat-lampiran').attr('href', '#');
        });
    }
}

$(document).ready(() => {
    window.MutasiSaldoApp = new MutasiSaldoModule();
});
