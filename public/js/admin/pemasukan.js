class PemasukanApp {
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
        this.resetFormOnModalClose();
        this.handleKategoriTransaksiChange();

        $('.select2').select2({ theme: 'bootstrap4', placeholder: '-- Pilih --', allowClear: false,  dropdownParent: $('#modalForm')});
    }

    handleKategoriTransaksiChange() {
        $('#id_kategori_transaksi').on('change', function () {
            const selected = $(this).find('option:selected').data('nama');
            
            if (selected === 'Pembayaran Piutang') {
                // Tambahkan select baru kalau belum ada
                if (!$('#id_piutang').length) {
                    const html = `
                        <div class="form-group row mb-3" id="wrapper-piutang">
                            <label for="id_piutang" class="col-sm-4 col-form-label">Pilih Piutang</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="id_piutang" name="id_piutang">
                                    <option value="">-- Pilih Piutang --</option>
                                    
                                </select>
                            </div>
                        </div>
                    `;
                    $('#wrapper-select-piutang').html(html);
                    $('.select2').select2({ theme: 'bootstrap4' });
                }
            } else {
                // Hapus jika bukan Pembayaran Piutang
                $('#wrapper-select-piutang').empty();
            }
        });
    }
    
    initDataTable() {
        const showAction = this.permissions.edit || this.permissions.hapus;
        this.table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: window.routes.index,
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal' },
                { data: 'nominal' },
                { data: 'kategori', name: 'kategoriTransaksi.nama_kategori' },
                { data: 'bank', name: 'bank.nama_bank' },
                { data: 'keterangan' },
                { data: 'action', orderable: false, searchable: false },
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
                            input.parent().append('<span class="invalid-feedback"><strong>' + val[0] + '</strong></span>');
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
                    $('#tanggal').val(data.tanggal);
                    $('#id_bank').val(data.id_bank).trigger('change');
                    $('#id_kategori_transaksi').val(data.id_kategori_transaksi).trigger('change');
                    $('#nominal').val(data.nominal);
                    $('#keterangan').val(data.keterangan);
                }
            });
        });
    }

    handleDelete() {
        $(document).on('click', '.delete-button', (e) => {
            e.preventDefault();
            const form = $(e.currentTarget).closest('form');

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

    resetFormOnModalClose() {
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
    window.PemasukanApp = new PemasukanApp();
});
