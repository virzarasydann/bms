class PengeluaranModule {
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
        this.handleKategoriTransaksiChange();
        this.handleSisaBayar();
        this.initCleave();
    }

    initCleave() {
        this.cleaveRupiahFields = [];
    
        const cleaveInputs = ['#nominal','#sisa_bayar'];
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

    handleKategoriTransaksiChange() {
        $('#id_kategori_transaksi').on('change', function () {
            const selected = $(this).find('option:selected').data('nama');
        
            if (selected === 'Pembayaran Hutang') {
                $('#wrapper-select-hutang').show();
              
            } else {
                $('#wrapper-select-hutang').hide();
                $('#id_hutang').val('').trigger('change'); // reset saat disembunyikan
            }
        });
    }

    handleSisaBayar() {
        const self = this;
        $('#id_hutang').on('change', function() {
           
            let selectedOption = $(this).find('option:selected');
            
            
            let nominal = selectedOption.data('nominal') || 0; 
            
          
            $('#sisa_bayar').val(nominal);
            self.initCleave();
            $('#show-sisa-bayar').show();
        });
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
                { data: 'nominal' },
                { data: 'kategori', name: 'kategoriTransaksi.nama_kategori' },
                { data: 'bank', name: 'bank.nama_bank' },
                { data: 'lampiran' },
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

            $.get(url, (res) => {
                console.log(res.data);
                if (res.status === 'success') {
                    const data = res.data;
                    
                        
                    
                        $('#primary_id').val(data.id);
                        $('#id_bank').val(data.id_bank).trigger('change').prop('disabled', true);
                        $('#id_kategori_transaksi').val(data.id_kategori_transaksi).trigger('change').prop('disabled', true);
                        $('#tanggal').val(data.tanggal).prop('disabled', true);
                        $('#nominal').val(data.nominal).prop('disabled', true);
                        $('#keterangan').val(data.keterangan).prop('disabled', true);
                        $('#id_hutang').val(data.id_hutang).trigger('change').prop('disabled', true);
                        
                        if (res.lampiran_url) {
                            $('#lampiran').prop('disabled',true)
                            $('#lihat-lampiran')
                                .attr('href', res.lampiran_url)
                                .text('Lihat Lampiran')
                                
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
            console.log(url)
            let html = '';
    
            if (url.match(/\.(jpg|jpeg|png)$/i)) {
                html = `<img src="${url}" class="img-fluid" alt="Lampiran">`;
            } else if (url.match(/\.(pdf)$/i)) {
                html = `<embed src="${url}" type="application/pdf" width="100%" height="600px" />`;
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
            $('#show-sisa-bayar').hide();
            $('#id_bank').prop('disabled', false);
            $('#id_kategori_transaksi').prop('disabled', false);
            $('#tanggal').prop('disabled', false);
            $('#nominal').prop('disabled', false);
            $('#keterangan').prop('disabled', false);
            $('#id_hutang').prop('disabled', false);
            $('#lampiran').prop('disabled',false)
        });
    }
}

$(document).ready(() => {
    window.PengeluaranApp = new PengeluaranModule();
});
