class PiutangModule {
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
            placeholder: '-- Pilih --',
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
                { data: 'tanggal_piutang' },
                { data: 'deskripsi' },
                { data: 'nominal' },
                { data: 'status' },
                { data: 'lampiran' },
                
                
                
               
                { data: 'tgl_pelunasan' },
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
                    toastr.success("Data telah disimpan!", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    this.reloadTable();
                },
                error: (xhr) => {
                    if (xhr.status === 422) {
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
        $(document).on('click', '.edit-button', function () {
            const url = $(this).data('url');
            $.get(url, (res) => {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#primary_id').val(data.id);
                    $('#id_bank').val(data.id_bank).trigger('change');
                    $('#tanggal_piutang').val(data.tanggal_piutang);
                    $('#nominal').val(data.nominal);
                    $('#deskripsi').val(data.deskripsi);
                    $('#status').val(data.status);
                    $('#terbayar').val(data.terbayar);
                    $('#sisa_bayar').val(data.sisa_bayar);
                    $('#tgl_pelunasan').val(data.tgl_pelunasan);


                    if (res.lampiran_url) {
                        $('#lihat-lampiran').attr('href', res.lampiran_url).text('Lihat Lampiran');
                        $('#lihat-lampiran-wrapper').removeClass('d-none');
                    } else {
                        $('#lihat-lampiran-wrapper').addClass('d-none');
                    }
                    $('#modalForm').modal('show');
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
                            toastr.success("Data telah dihapus!", "BERHASIL", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                            this.table.ajax.reload();
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
        $('#modalForm').on('hidden.bs.modal', () => {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.select2').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        });
    }
}

$(function () {
    window.PiutangApp = new PiutangModule();
});
