@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-xl mb-3">Data Pengeluaran</h1>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-medium text-lg">Data</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm" id="tombol-tambah"><i class="fas fa-plus"></i>
                                                Tambah Pengeluaran</button>
                                        @endif

                                        <a href="{{ route('cetak-pengeluaran') }}" target="_blank"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-file-pdf"></i> Cetak PDF
                                        </a>
                                        <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                            Reload
                                        </button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped data-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Nama Mustahik</th>
                                                <th>Jumlah</th>

                                                <th>Lampiran</th>
                                                <th width="100px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="modalFormLabel">Form Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">

                        <div class="form-group row mb-3">
                            <label for="tanggal_pengeluaran" class="col-sm-4 col-form-label">Tanggal</label>
                            <div class="col-sm-3">
                                <input type="date" id="tanggal_pengeluaran" class="form-control"
                                    name="tanggal_pengeluaran" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="no_pengeluaran" class="col-sm-4 col-form-label">No Pengeluaran</label>
                            <div class="col-sm-4">
                                <input type="text" id="no_pengeluaran" class="form-control" name="no_pengeluaran"
                                    readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">Mustahik</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="nama_lengkap" name="nama_lengkap">
                                </select>
                            </div>
                        </div>



                        <div class="form-group row mb-3">
                            <label for="file_upload" class="col-sm-4 col-form-label">Upload File</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="file" id="file_upload" name="file_upload">
                                </div>

                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="tipe_saldo" class="col-sm-4 col-form-label">Tipe</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="tipe_saldo" name="tipe_saldo">

                                    <option value="Bank">Bank</option>
                                    <option value="Kas">Kas</option>
                                </select>
                            </div>
                        </div>



                        <div class="form-group row mb-3">
                            <label for="deskripsi" class="col-sm-4 col-form-label">Deskripsi</label>
                            <div class="col-sm-8">
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
                            </div>
                        </div>



                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label">List Pengeluaran</label>
                            <div class="col-sm-12">
                                <div id="barang-container">
                                    <div class="barang-row border p-3 rounded mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-sm-5">
                                                <select class="form-control select2-dynamic"
                                                    name="kategori_pengeluaran_id[]">
                                                    <!-- Select2 AJAX -->
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control nominal" name="nominal[]"
                                                    placeholder="Nominal">
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-danger hapus-baris w-100">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- ⬇ Tempat sumber dana -->
                                        <div class="sumber-dana-wrapper mt-3"></div>

                                        <!-- ⬇ Tombol tambah sumber dana -->
                                        <div class="text-end mt-2">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm tambah-sumber-dana">
                                                + Tambah Sumber Dana
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3" id="wrapper-tambah-baris">
                                    <button type="button" class="btn btn-primary tambah-baris">Tambah Baris</button>
                                </div>
                            </div>
                        </div>






                    </div>

                    <div class="modal-footer" id="wrapper-footer">
                        <button type="submit" id="submitBtn" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLampiran" tabindex="-1" role="dialog" aria-labelledby="modalLampiranLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="modalFormLabel">Lampiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <div id="lampiranPreview"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLampiran" tabindex="-1" aria-labelledby="modalLampiranLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lampiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewLampiran" src="#" alt="Lampiran" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function loadSumberDanaOptions(selectElement) {
            $.ajax({
                url: '{{ route('kategori-pemasukan.list') }}',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Kosongkan dulu
                    selectElement.empty();
                    selectElement.append('<option value="">Pilih Sumber Dana</option>');
                    data.forEach(function(item) {
                        const option = $('<option></option>')
                            .val(item.id)
                            .text(item.nama + ' - ' + item.jenis_kategori);
                        selectElement.append(option);
                    });
                },
                error: function() {
                    alert('Gagal memuat kategori pemasukan.');
                }
            });
        }

        // Tambah sumber dana
        $(document).on('click', '.tambah-sumber-dana', function() {
            const barangRow = $(this).closest('.barang-row');
            const index = barangRow.index(); // ← ambil index baris keberapa

            const sumberDanaHTML = `
        <div class="row sumber-dana-row align-items-center mb-2">
            <div class="col-md-5">
                <select class="form-control sumber-dana-select" name="sumber_dana_jenis[${index}][]">
                    <option value="">Pilih Sumber Dana</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control sumber-nominal" name="sumber_nominal[${index}][]" placeholder="Nominal">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger hapus-sumber-dana w-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

            const container = barangRow.find('.sumber-dana-wrapper');
            const element = $(sumberDanaHTML);
            container.append(element);
            loadSumberDanaOptions(element.find('select'));
            inisialisasiCleave();
        });


        // Hapus sumber dana
        $(document).on('click', '.hapus-sumber-dana', function() {
            $(this).closest('.sumber-dana-row').remove();
        });

        // Tambah baris pengeluaran baru
        $('.tambah-baris').click(function() {
            let barisBaru = $(`
        <div class="barang-row border p-3 rounded mb-3">
            <div class="row align-items-center">
                <div class="col-sm-5">
                    <select class="form-control select2-dynamic" name="kategori_pengeluaran_id[]"></select>
                </div>
                <div class="col-sm-3">
                    <input type="text" class="form-control nominal" name="nominal[]" placeholder="Nominal">
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger hapus-baris w-100">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="sumber-dana-wrapper mt-3"></div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-outline-primary btn-sm tambah-sumber-dana">
                    + Tambah Sumber Dana
                </button>
            </div>
        </div>
    `);
            $('#barang-container').append(barisBaru);
            inisialisasiSelect2(barisBaru.find('.select2-dynamic'));
            inisialisasiCleave(); // jika pakai cleave.js
        });

        // Hapus baris pengeluaran
        $(document).on('click', '.hapus-baris', function() {
            $(this).closest('.barang-row').remove();
        });

        // Inisialisasi Select2
        function inisialisasiSelect2(selectElement) {
            selectElement.select2({
                placeholder: 'Pilih Kategori',
                ajax: {
                    url: '{{ route('kategori.pengeluaran.list') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.nama + ' - ' + item.jenis_kategori
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }

        // Inisialisasi Cleave (optional)
        function inisialisasiCleave() {
            $('.nominal, .sumber-nominal').each(function() {
                if (!this.cleave) {
                    this.cleave = new Cleave(this, {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand',
                        delimiter: '.',
                        numeralDecimalScale: 0,
                        numeralDecimalMark: ','
                    });
                }
            });
        }

        // Inisialisasi awal
        $(document).ready(function() {
            inisialisasiSelect2($('.select2-dynamic'));
            inisialisasiCleave();
        });




        var audio = new Audio('{{ asset('audio/notification.ogg') }}');
        $(document).ready(function() {
            $('#tombol-tambah').on('click', function() {
                const tanggal = $('#tanggal_pengeluaran').val();

                // Kalau tanggal sudah dipilih, langsung generate
                if (tanggal) {
                    fetchNomor(tanggal);
                } else {
                    // Atau pakai tanggal sekarang kalau belum diisi
                    fetchNomor();
                }
            });

            function fetchNomor(tanggal = null) {
                $.ajax({
                    url: "{{ route('penomoran') }}",
                    type: "GET",
                    data: tanggal ? {
                        tanggal_pengeluaran: tanggal
                    } : {},
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#no_pengeluaran').val(response.data);
                        } else {
                            alert('Gagal mendapatkan nomor pengeluaran.');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat memuat nomor pengeluaran.');
                    }
                });
            }




            $('#tombol-tambah').click(function() {
                $('#formData')[0].reset();
                $('#barang-container .barang-row').remove();
                $('.tambah-baris').click();

                $('#nama_lengkap').val(null).trigger('change');
                $('#modalForm')
                    .data('mode', 'create')
                    .modal('show');

            });


            // Inisialisasi Select2 untuk kategori pengeluaran
            function inisialisasiSelect2(selectElement) {
                selectElement.select2({
                    placeholder: 'Pilih Kategori',
                    ajax: {
                        url: '{{ route('kategori.pengeluaran.list') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.nama + ' - ' + item.jenis_kategori
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }

            // Inisialisasi Cleave.js untuk semua input nominal
            // function inisialisasiCleave() {
            //     $('.nominal').each(function() {
            //         if (!this.cleave) {
            //             this.cleave = new Cleave(this, {
            //                 numeral: true,
            //                 numeralThousandsGroupStyle: 'thousand',
            //                 delimiter: '.',
            //                 numeralDecimalScale: 0,
            //                 numeralDecimalMark: ','
            //             });
            //         }
            //     });
            // }

            // Tambah baris baru
            // $('.tambah-baris').click(function() {
            //     let barisBaru = $(`
        //     <div class="barang-row row align-items-center mb-3">
        //         <div class="col-sm-5">
        //             <select class="form-control select2-dynamic" name="kategori_pengeluaran_id[]"></select>
        //         </div>
        //         <div class="col-sm-2">
        //             <input type="text" class="form-control nominal" name="nominal[]" placeholder="Nominal">
        //         </div>
        //         <div class="col-md-1">
        //             <button type="button" class="btn btn-danger hapus-baris w-100">
        //                 <i class="fas fa-trash"></i>
        //             </button>
        //         </div>
        //     </div>
        // `);
            //     $('#barang-container').append(barisBaru);
            //     inisialisasiSelect2(barisBaru.find('.select2-dynamic'));
            //     inisialisasiCleave();
            // });

            // Hapus baris
            $(document).on('click', '.hapus-baris', function() {
                $(this).closest('.barang-row').remove();
            });

            // Inisialisasi awal
            inisialisasiSelect2($('select[name="kategori_pengeluaran_id[]"]'));
            inisialisasiCleave();

            // Select2 untuk Mustahik
            $('#nama_lengkap').select2({
                ajax: {
                    url: '{{ route('pengeluaran.search') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.nama_lengkap
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            // Tabel DataTables
            var permissions = @json($permissions);
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('pengeluaran.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'tanggal_pengeluaran',
                        name: 'tanggal_pengeluaran'
                    },
                    {
                        data: 'id_mustahik',
                        name: 'id_mustahik'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
                    },
                    {
                        data: 'lampiran',
                        name: 'lampiran'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ]
            });

            let id = [];

            // Klik Edit
            $(document).on('click', '.edit-btn', function() {
                $('#modalForm').data('mode', 'edit');
                id = $(this).data('id');
                $('#wrapper-tambah-baris').hide();
                const url = $(this).data('url');

                $('#formData')[0].reset();
                $('#formData').find('input[type="file"]').val('');
                $('#formData').find('select').val(null).trigger('change');
                $('#barang-container').html('');
                $('#primary_id').val(id);

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(res) {
                        if (res.status === 'success') {
                            const data = res.data;
                            $('#tanggal_pengeluaran').val(data.tanggal_pengeluaran);
                            $('#deskripsi').val(data.deskripsi);
                            $('#tipe_saldo').val(data.tipe).trigger('change');
                            $('#no_pengeluaran').val(data.no_pengeluaran);
                            const option = new Option(data.mustahik.nama_lengkap, data
                                .id_mustahik, true, true);
                            $('#nama_lengkap').append(option).trigger('change');

                            data.detail.forEach(function(item, index) {
                                let sumberHTML = '';

                                item.sumber_dana.forEach(function(sumber) {
                                    sumberHTML += `
                            <div class="row sumber-dana-row align-items-center mb-2" data-sumber-id="${sumber.id}">
                                <div class="col-md-5">
                                    <select class="form-control sumber-dana-select" name="sumber_dana_jenis[${index}][]">
                                        <option value="${sumber.sumber_dana_id}" selected>${sumber.nama} - ${sumber.jenis_kategori}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control sumber-nominal" name="sumber_nominal[${index}][]" value="${sumber.nominal}">
                                </div>
                               
                            </div>
                        `;
                                });

                                const row = `
                                            <div class="barang-row border p-3 rounded mb-3">
                                                <div class="row align-items-center">
                                                    <input type="hidden" name="detail_id[]" value="${item.id}">
                                                    <div class="col-sm-5">
                                                        <select class="form-control select2" name="kategori_pengeluaran_id[]">
                                                            <option value="${item.kategori_pengeluaran_id}" selected>
                                                                ${item.kategori?.nama ?? '-'} - ${item.kategori?.jenis_kategori ?? ''}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control nominal" name="nominal[]" value="${item.nominal}">
                                                    </div>
                                                    <!-- tidak ada tombol hapus baris -->
                                                </div>

                                                <div class="sumber-dana-wrapper mt-3">
                                                    ${sumberHTML}
                                                </div>

                                               
                                            </div>
                                            `;


                                $('#barang-container').append(row);
                            });

                            $('.select2').select2();
                            inisialisasiCleave(); // pastikan ini ada
                            $('#modalForm').modal('show');
                        }
                    },
                    error: function() {
                        alert('Gagal mengambil data.');
                    }
                });
            });


            // Update nominal via blur
            $(document).on('blur', '.nominal', function() {
                const nominalInput = $(this);
                const row = nominalInput.closest('.barang-row');
                const detailId = row.find('input[name="detail_id[]"]').val();
                const kategoriIdRaw = row.find('select[name="kategori_pengeluaran_id[]"]').val();
                const kategoriId = Array.isArray(kategoriIdRaw) ? kategoriIdRaw[0] : kategoriIdRaw;

                const nominal = nominalInput[0].cleave.getRawValue(); // ← string clean dari Cleave.js

                if (!detailId || !kategoriId || !nominal) {
                    console.warn('Data tidak lengkap, update dibatalkan');
                    return;
                }

                $.ajax({
                    url: `/admin/pengeluaran/detail/update`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        
                        detail_id: detailId,
                        kategori_pengeluaran_id: kategoriId,
                        nominal: nominal // ← string, bukan array!
                    },
                    success: function(response) {
                        audio.play();
                        toastr.success(response.status || "Berhasil diperbarui", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: "toast-bottom-right"
                        });
                        table.ajax.reload(null, false);
                    },
                    error: function() {
                        console.error('Update gagal');
                    }
                });
            });

            $(document).on('blur', '.sumber-nominal, .sumber-dana-select', function() {
                const sumberRow = $(this).closest('.sumber-dana-row');
                const sumberDanaId = sumberRow.data('sumber-id'); // ← gunakan data attribute
                const sumberDanaJenis = sumberRow.find('.sumber-dana-select').val();
                const nominalRaw = sumberRow.find('.sumber-nominal').val().replace(/\./g, '');
                const isEditMode = $('#modalForm').data('mode') === 'edit';

                if (isEditMode) {
                    if (!sumberDanaId || !sumberDanaJenis || !nominalRaw) {
                        alert('Data tidak lengkap, update dibatalkan');
                        return;
                    }

                    $.ajax({
                        url: `/admin/pengeluaran/sumber/update`, // ← gunakan ID pengeluaran
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                           
                            sumber_dana_id: sumberDanaId,
                            sumber_dana_jenis: sumberDanaJenis,
                            sumber_nominal: nominalRaw,
                        },
                        success: function(res) {
                            audio.play();
                            toastr.success(res.status || "Berhasil diperbarui", "BERHASIL", {
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: "toast-bottom-right"
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function() {
                            console.error('Gagal memperbarui sumber dana');
                        }
                    });
                }

            });
            // Submit form simpan/update
            $('#formData').on('submit', function(e) {
                e.preventDefault();
                let id = $('#primary_id').val();
                
                let url = id ? '{{ route('pengeluaran.update', ['pengeluaran' => ':id']) }}'.replace(':id',
                    id) : '{{ route('pengeluaran.store') }}';
                let method = id ? 'PUT' : 'POST';
               
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();


                $('.nominal').each(function() {
                    if (this.cleave) {
                        $(this).val(this.cleave.getRawValue());
                    } else {
                        // fallback jika cleave tidak tersedia (tidak ter-inisialisasi)
                        let value = $(this).val().replace(/\./g, '').replace(/[^0-9]/g, '');
                        $(this).val(value);
                    }
                });


                let formData = new FormData(this);
                formData.append('_method', method);
               
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        $('#modalForm').modal('hide');
                        audio.play();
                        toastr.success("Data telah disimpan!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                        $('.data-table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            audio.play();
                            toastr.error("Ada inputan yang salah!", "GAGAL!", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });

                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                let input = $('#' + key);
                                input.addClass('is-invalid');
                                input.parent().find('.invalid-feedback').remove();
                                input.parent().append(
                                    '<span class="invalid-feedback" role="alert"><strong>' +
                                    val[0] + '</strong></span>'
                                );
                            });
                        }
                    }
                });
            });

            // Reset modal
            $('#modalForm').on('hidden.bs.modal', function() {
                $(this).removeData('mode'); // reset mode
                $('#wrapper-tambah-baris').show();
                $('#wrapper-footer').show();

                $('#formData')[0].reset();
                $('#nama_lengkap').val(null).trigger('change');
                $('#barang-container').html('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.tambah-baris').click();
            });


            $(document).on('click', '.btn-lampiran', function() {
                const url = $(this).data('url');
                let ext = url.split('.').pop().toLowerCase();
                let preview = '';

                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                    preview =
                        `<img src="${url}" style="max-width: 400px; heigth: auto;" class="img-thumbnail" alt="Lampiran">`;
                } else if (ext === 'pdf') {
                    preview = `<iframe src="${url}" width="100%" height="600px"></iframe>`;
                } else {
                    preview = `<p>File tidak dapat ditampilkan.</p>`;
                }

                $('#lampiranPreview').html(preview);
                $('#modalLampiran').modal('show');
            });


            $(document).on('submit', 'form', function(e) {
                if ($(this).has('button.delete-button').length) {
                    e.preventDefault();

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function() {
                            audio.play();
                            toastr.success("Data telah dihapus!", "BERHASIL", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                            $('.data-table').DataTable().ajax.reload();
                        },
                        error: function() {
                            audio.play();
                            toastr.error("Gagal menghapus data.", "GAGAL!", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                        }
                    });

                }
            });

            $(document).on('click', '.delete-button', function(e) {
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
                        form.submit();
                    }
                });
            });

        });
    </script>
@endpush
