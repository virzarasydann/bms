class HutangModule {
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
        this.initCleave();
    }

    initSelect2() {
        $('.select2').select2({
            theme: "bootstrap4",
            allowClear: false,
            dropdownParent: $('#modalForm')
        });
    }

    initCleave() {
        this.cleaveRupiahFields = [];
    
        const cleaveInputs = ['#nominal'];
        cleaveInputs.forEach(selector => {
            if ($(selector).length) {
                const cleave = new Cleave(selector, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.',
                    numeralDecimalScale: 0,
                });
                this.cleaveRupiahFields.push(cleave);
            }
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
                { data: 'tanggal_hutang' },
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
                            input.parent().append(`<span class="invalid-feedback"><strong>${val[0]}</strong></span>`);
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

            $.get(url, (res) => {
                if (res.status === 'success') {
                    const d = res.data;
                    $('#primary_id').val(d.id);
                    $('#tanggal_hutang').val(d.tanggal_hutang);
                    $('#deskripsi').val(d.deskripsi);
                    $('#id_bank').val(d.id_bank).trigger('change');
                    $('#nominal').val(d.nominal);
                    $('#status').val(d.status);
                    $('#terbayar').val(d.terbayar);
                    $('#sisa_bayar').val(d.sisa_bayar);
                    $('#tgl_pelunasan').val(d.tgl_pelunasan);

                    if (res.lampiran_url) {
                        $('#lihat-lampiran').attr('href', res.lampiran_url);
                        $('#lihat-lampiran-wrapper').removeClass('d-none');
                    } else {
                        $('#lihat-lampiran-wrapper').addClass('d-none');
                    }

                    self.initCleave();
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
        });
    }
}

$(document).ready(() => {
    window.HutangApp = new HutangModule();
});


