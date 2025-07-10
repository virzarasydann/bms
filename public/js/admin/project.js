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
        $(document).on('click', '#edit-button', function () {
            const url = $(this).data('url');

            $.get(url, function (res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#primary_id').val(data.id);
                    $('#nama_project').val(data.nama_project);
                    $('#id_customer').val(data.id_customer).trigger('change');
                    $('#id_kategori_project').val(data.id_kategori_project).trigger('change');
                    $('#tgl_kontrak').val(data.tgl_kontrak);
                    $('#tanggal_selesai').val(data.tanggal_selesai);
                    $('#nilai_project').val(data.nilai_project);
                    $('#penanggung_jawab').val(data.penanggung_jawab);
                    $('#status_pembayaran').val(data.status_pembayaran).trigger('change');
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
        });
    }
}

$(document).ready(() => {
    window.ProjectApp = new ProjectModule();
});
