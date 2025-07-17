class ProjectModule {
    constructor() {
        this.table = null;
        this.audio = new Audio('/audio/notification.ogg');
        this.permissions = window.permissions || {};
        this.routes = window.routes || {};
        this.init();
    }

    init() {
       
        this.initSelect2();
        this.initDataTable();
        this.handleFormSubmit();
        this.handleEdit();
        this.handleDelete();
        this.resetModalOnClose();
        this.handleStatusPembayaranChange();
        this.initCleave();
    }


    initCleave() {
        this.cleaveRupiahFields = [];
    
        const cleaveInputs = ['#nilai_project', '#nilai_dp'];
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

    

    handleStatusPembayaranChange() {
        const self = this;
        $(document).on('change', '#status_pembayaran', function () {
            const value = $(this).val();
    
            
    
            if (value === 'DP') {
                $('#wrapper-nominal-dp-container').html(`
                    <label for="nilai_dp" class="col-sm-4 col-form-label">Nominal DP</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control" id="nilai_dp" name="nilai_dp" ">
                        </div>
                    </div>
                `);
                self.initCleave();
                
            } else {
                $('#wrapper-nominal-dp-container').html('');
            }
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

    initDataTable() {
        const showAction = this.permissions.edit || this.permissions.hapus;

        this.table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: this.routes.index,
            ordering: false,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_project', name: 'nama_project' },
                { data: 'customer', name: 'customer.nama' },
                { data: 'tgl_kontrak', name: 'tgl_kontrak' },
                { data: 'tanggal_selesai', name: 'tanggal_selesai' },
                { data: 'penanggung_jawab', name: 'penanggung_jawab' },
                { data: 'status_pembayaran', name: 'status_pembayaran' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [{
                targets: 0,
                render: (data, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }]
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
                ? this.routes.update.replace(':id', id)
                : this.routes.store;
            const method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            const formData = new FormData(e.target);
            formData.append('_method', method);

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
                                `<span class="invalid-feedback" role="alert"><strong>${val[0]}</strong></span>`
                            );
                        });
                    }
                }
            });
        });
    }

    handleEdit() {
        const self = this
        $(document).on('click', '#edit-button', function () {
            const url = $(this).data('url');
            
            $.get(url, function (res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#primary_id').val(data.id);
                    $('#nama_project').val(data.nama_project).prop('disabled', true);
                    $('#id_customer').val(data.id_customer).trigger('change').prop('disabled', true);
                    $('#id_kategori_project').val(data.id_kategori_project).trigger('change').prop('disabled', true);;
                    $('#tgl_kontrak').val(data.tgl_kontrak).prop('disabled', true);;
                    $('#tanggal_selesai').val(data.tanggal_selesai);
                    $('#nilai_project').val(data.nilai_project).prop('disabled', true);;
                    $('#penanggung_jawab').val(data.penanggung_jawab);
                    $('#status_pembayaran').val(data.status_pembayaran).trigger('change').prop('disabled', true);
                    $('#id_bank').val(res.id_bank).trigger('change');
                    $('#nilai_dp').val(res.nominal_dp).prop('disabled', true);
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
                            this.audio.play();
                            toastr.success("Data telah dihapus!", "BERHASIL", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                            this.reloadTable();
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
        });
    }

    resetModalOnClose() {
        $('#modalForm').on('hidden.bs.modal', () => {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.select2').val('').trigger('change');
            $('#wrapper-nominal-dp-container').html('');
            $('#nama_project').prop('disabled', false);
            $('#tgl_kontrak').prop('disabled', false);
            $('#nilai_project').prop('disabled', false);
            $('#id_customer').prop('disabled', false);
            $('#id_kategori_project').prop('disabled', false);
            $('#status_pembayaran').prop('disabled', false);
            $('#nilai_dp').prop('disabled', false);
        });
    }
}

$(document).ready(() => {
    window.ProjectApp = new ProjectModule();
});
