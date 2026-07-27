@extends('templates.template')

@section('page_title', 'Form Pengajuan Peminjaman')
@section('page_subtitle', 'Isi detail peminjaman ruangan')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    /* ============================================================
       FORM PEMINJAMAN – Redesign Modern & Clean
       ============================================================ */
    .form-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1200px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }

    .greeting-section {
        margin-bottom: var(--space-5);
    }
    .greeting-section .greeting-title {
        font-size: var(--font-size-xl);
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.01em;
    }
    .greeting-section .greeting-sub {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: var(--space-1) 0 0 0;
        display: flex;
        align-items: center;
        gap: var(--space-2);
        flex-wrap: wrap;
    }

    .form-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .form-card .card-header {
        padding: var(--space-4) var(--space-6);
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-2);
    }
    .form-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .form-card .card-header .title i {
        color: var(--brand-orange);
        font-size: 1.2rem;
    }
    .form-card .card-header .badge-status {
        padding: 4px 14px;
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        background: rgba(245, 158, 11, 0.10);
        color: #d97706;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .form-card .card-body {
        padding: var(--space-6);
    }

    /* ============================================================
       TWO COLUMN LAYOUT – Proporsional
       ============================================================ */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-6);
    }

    @media (max-width: 991.98px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: var(--space-4);
        }
        .form-content {
            padding: var(--space-3);
        }
        .form-card .card-body {
            padding: var(--space-4);
        }
        .form-card .card-header {
            padding: var(--space-3) var(--space-4);
        }
    }

    @media (max-width: 575.98px) {
        .form-content {
            padding: var(--space-2);
        }
        .form-card .card-body {
            padding: var(--space-3);
        }
        .form-card .card-header {
            padding: var(--space-2) var(--space-3);
            flex-direction: column;
            align-items: flex-start;
        }
        .datetime-wrapper {
            flex-wrap: wrap;
        }
        .datetime-wrapper .date-input {
            flex: 1 1 100%;
        }
        .datetime-wrapper .time-input {
            flex: 1 1 40%;
        }
    }

    /* ============================================================
       FORM GROUP
       ============================================================ */
    .form-group {
        margin-bottom: var(--space-4);
    }
    .form-group .form-label {
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        display: block;
        margin-bottom: var(--space-1);
    }
    .form-group .form-label .text-danger {
        color: #ef4444;
    }
    .form-group .form-label .text-muted {
        font-weight: 400;
        color: var(--text-muted);
        font-size: var(--font-size-xs);
    }
    .form-control,
    .form-select {
        height: 44px;
        padding: 0 var(--space-3);
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        width: 100%;
        transition: border var(--transition-fast), box-shadow var(--transition-fast), background var(--transition-fast);
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--brand-orange);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.06);
        outline: none;
    }
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #ef4444;
    }
    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.10);
    }
    textarea.form-control {
        height: auto;
        min-height: 100px;
        resize: vertical;
        padding: var(--space-2) var(--space-3);
    }
    .invalid-feedback {
        font-size: var(--font-size-xs);
        color: #ef4444;
        margin-top: var(--space-1);
    }

    /* ============================================================
       DATE TIME PICKER – Clean & Intuitive
       ============================================================ */
    .datetime-wrapper {
        display: flex;
        gap: var(--space-2);
        align-items: center;
        flex-wrap: wrap;
    }
    .datetime-wrapper .date-input {
        flex: 2;
        position: relative;
        min-width: 150px;
    }
    .datetime-wrapper .time-input {
        flex: 1;
        position: relative;
        min-width: 100px;
    }
    .datetime-wrapper .form-control,
    .datetime-wrapper .form-select {
        padding-left: 36px;
        height: 44px;
    }
    .datetime-wrapper .input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1rem;
        pointer-events: none;
        z-index: 2;
    }
    .datetime-wrapper .time-separator {
        color: var(--text-muted);
        font-weight: 500;
        font-size: var(--font-size-sm);
    }

    /* ============================================================
       AVAILABILITY STATUS – Real-time indicator
       ============================================================ */
    .availability-status {
        padding: var(--space-2) var(--space-4);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-sm);
        display: none;
        align-items: center;
        gap: var(--space-2);
        margin-top: var(--space-2);
        animation: fadeUp 0.3s ease forwards;
    }
    .availability-status.visible {
        display: flex;
    }
    .availability-status.loading {
        background: rgba(59, 130, 246, 0.06);
        color: var(--brand-blue-dark);
        border: 1px solid rgba(59, 130, 246, 0.10);
    }
    .availability-status.available {
        background: rgba(16, 185, 129, 0.08);
        color: var(--brand-green-dark);
        border: 1px solid rgba(16, 185, 129, 0.15);
    }
    .availability-status.unavailable {
        background: rgba(239, 68, 68, 0.06);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.10);
    }
    .availability-status .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(59, 130, 246, 0.15);
        border-top-color: var(--brand-blue-dark);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        flex-shrink: 0;
    }
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ============================================================
       ROOM RECOMMENDATION
       ============================================================ */
    .recommended-slots {
        margin-top: var(--space-2);
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .recommended-slots .slot-btn {
        padding: 4px 14px;
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 500;
        background: rgba(59, 130, 246, 0.06);
        color: var(--brand-blue-dark);
        border: 1px solid rgba(59, 130, 246, 0.10);
        cursor: pointer;
        transition: all var(--transition-fast);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .recommended-slots .slot-btn:hover {
        background: rgba(59, 130, 246, 0.12);
        transform: translateY(-1px);
    }
    .recommended-slots .slot-btn i {
        font-size: 0.7rem;
    }

    /* ============================================================
       SELECT2 OVERRIDE – Multi-select modern
       ============================================================ */
    .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 44px;
        border-color: var(--border-color);
        border-radius: var(--radius-sm);
        background: var(--bg-input);
        padding: 2px 6px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background: rgba(249, 115, 22, 0.08);
        border: 1px solid rgba(249, 115, 22, 0.15);
        color: var(--brand-orange-dark);
        border-radius: var(--radius-pill);
        padding: 2px 12px;
        font-size: var(--font-size-xs);
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-right: 2px;
        cursor: pointer;
        transition: color var(--transition-fast);
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ef4444;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__placeholder {
        color: var(--text-muted);
        font-size: var(--font-size-sm);
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: var(--border-color);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-dropdown);
    }
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background: rgba(249, 115, 22, 0.06);
        color: var(--brand-orange-dark);
    }
    .select2-container--bootstrap-5 .select2-results__option--selected {
        background: rgba(249, 115, 22, 0.08);
        color: var(--brand-orange-dark);
    }

    /* ============================================================
       BUTTONS
       ============================================================ */
    .btn-submit {
        height: 44px;
        padding: 0 var(--space-6);
        background: var(--brand-gradient);
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-inverse);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.15);
    }
    .btn-submit:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.20);
        color: var(--text-inverse);
    }
    .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    .btn-cancel {
        height: 44px;
        padding: 0 var(--space-5);
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        text-decoration: none;
    }
    .btn-cancel:hover {
        border-color: var(--brand-orange);
        color: var(--brand-orange-dark);
        background: rgba(249, 115, 22, 0.04);
    }

    /* ============================================================
       FACILITY QUANTITY
       ============================================================ */
    .facility-quantity {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        margin-bottom: var(--space-1);
    }
    .facility-quantity label {
        font-weight: 500;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        min-width: 100px;
    }
    .facility-quantity input {
        width: 80px;
        height: 36px;
        text-align: center;
        padding: 0 var(--space-2);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: var(--bg-input);
        font-size: var(--font-size-sm);
    }
    .facility-quantity input:focus {
        border-color: var(--brand-orange);
        outline: none;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.06);
    }

    /* ============================================================
       ANIMASI
       ============================================================ */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .form-card {
        animation: fadeUp 0.4s ease forwards;
    }
</style>

<div class="form-content">

    <div class="greeting-section">
        <h1 class="greeting-title">Form Pengajuan Peminjaman</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-calendar3 me-1"></i> Isi detail peminjaman ruangan</span>
        </div>
    </div>

    <div class="form-card">
        <div class="card-header">
            <div class="title">
                <i class="bi bi-pencil-square"></i> Form Peminjaman
            </div>
            <div>
                <span class="badge-status">
                    <i class="bi bi-clock"></i> Pending
                </span>
            </div>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger" style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.10); color:#dc2626; padding:var(--space-3) var(--space-4); border-radius:var(--radius-sm); margin-bottom:var(--space-4);">
                    <ul style="margin:0; padding-left:var(--space-4);">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                @csrf

                <div class="form-grid">

                    <!-- ============ LEFT COLUMN ============ -->
                    <div class="form-left">

                        <!-- Ruangan -->
                        <div class="form-group">
                            <label for="room_id" class="form-label">
                                <i class="bi bi-door-open me-1"></i> Ruangan <span class="text-danger">*</span>
                            </label>
                            <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}
                                            data-capacity="{{ $room->capacity }}">
                                        {{ $room->name }} (Kapasitas: {{ $room->capacity }} org)
                                    </option>
                                @endforeach
                            </select>
                            <div id="roomCapacityHint" class="form-text text-muted" style="font-size:var(--font-size-xs); margin-top:var(--space-1); display:none;">
                                <i class="bi bi-people"></i> Kapasitas: <span id="capacityDisplay">0</span> orang
                            </div>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal & Waktu -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-calendar3 me-1"></i> Tanggal & Waktu <span class="text-danger">*</span>
                            </label>
                            <div class="datetime-wrapper">
                                <div class="date-input">
                                    <i class="bi bi-calendar input-icon"></i>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ old('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                           min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                           required>
                                </div>
                                <div class="time-input">
                                    <i class="bi bi-clock input-icon"></i>
                                    <input type="time" name="start_time" id="start_time"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ old('start_time', \Carbon\Carbon::now()->format('H:i')) }}"
                                           required>
                                </div>
                                <span class="time-separator">sampai</span>
                                <div class="time-input">
                                    <i class="bi bi-clock input-icon"></i>
                                    <input type="time" name="end_time" id="end_time"
                                           class="form-control @error('end_time') is-invalid @enderror"
                                           value="{{ old('end_time', \Carbon\Carbon::now()->addHour()->format('H:i')) }}"
                                           required>
                                </div>
                            </div>
                            <small class="form-text text-muted" style="font-size:var(--font-size-xs);">
                                <i class="bi bi-info-circle me-1"></i> Jam kerja 07:00 – 16:00 WIB · Interval 30 menit
                            </small>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Availability Status -->
                        <div class="form-group">
                            <div id="availabilityStatus" class="availability-status">
                                <span class="spinner" id="availabilitySpinner"></span>
                                <span id="availabilityMessage">Mengecek ketersediaan...</span>
                            </div>
                        </div>

                        <!-- Recommended Slots -->
                        <div id="recommendedSlotsContainer" style="display:none; margin-top:var(--space-2);">
                            <label class="form-label" style="font-size:var(--font-size-xs); color:var(--text-muted);">
                                <i class="bi bi-clock-history me-1"></i> Slot Tersedia Lainnya
                            </label>
                            <div class="recommended-slots" id="recommendedSlots"></div>
                        </div>

                    </div>

                    <!-- ============ RIGHT COLUMN ============ -->
                    <div class="form-right">

                        <!-- Tujuan Peminjaman -->
                        <div class="form-group">
                            <label for="purpose" class="form-label">
                                <i class="bi bi-clipboard me-1"></i> Tujuan Peminjaman <span class="text-danger">*</span>
                            </label>
                            <textarea name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror"
                                      rows="4" placeholder="Jelaskan tujuan peminjaman ruangan..." required>{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size:var(--font-size-xs);">
                                Minimal 10 karakter, maksimal 500 karakter
                            </small>
                            <div id="purposeCounter" style="font-size:var(--font-size-xs); color:var(--text-muted); margin-top:4px; text-align:right;">
                                <span id="purposeCount">0</span> / 500
                            </div>
                        </div>

                        <!-- Fasilitas Tambahan -->
                        <div class="form-group">
                            <label for="facilities" class="form-label">
                                <i class="bi bi-tools me-1"></i> Fasilitas Tambahan <span class="text-muted">(Opsional)</span>
                            </label>
                            <select name="facilities[]" id="facilities" class="form-select select2 @error('facilities') is-invalid @enderror" multiple="multiple" style="width:100%;">
                                @foreach($facilities as $facility)
                                    <option value="{{ $facility->id }}" {{ is_array(old('facilities')) && in_array($facility->id, old('facilities')) ? 'selected' : '' }}>
                                        {{ $facility->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted" style="font-size:var(--font-size-xs);">
                                <i class="bi bi-info-circle me-1"></i> Pilih fasilitas tambahan yang diperlukan
                            </small>
                            @error('facilities')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Facilities Quantities -->
                        <div id="facilitiesQuantities" class="form-group {{ old('facilities') ? '' : 'd-none' }}">
                            <label class="form-label" style="font-size:var(--font-size-xs); color:var(--text-secondary);">
                                <i class="bi bi-hashtag me-1"></i> Jumlah Fasilitas
                            </label>
                            <div id="quantityInputs"></div>
                        </div>

                    </div>

                </div>

                <!-- ============ SUBMIT BUTTONS ============ -->
                <div style="margin-top: var(--space-5); border-top: 1px solid var(--border-color-light); padding-top: var(--space-4);">
                    <div class="d-flex justify-content-end gap-2 flex-wrap">
                        <a href="{{ route('bookings.index') }}" class="btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn-submit" id="submitBtn" disabled>
                            <i class="bi bi-save"></i> Ajukan Peminjaman
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {

        // ============================================================
        // 1. SELECT2 - Fasilitas Multi-Select
        // ============================================================
        $('#facilities').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih fasilitas...',
            allowClear: true,
            width: '100%'
        });

        const facilitiesData = @json($facilities->keyBy('id'));

        // Handle facility change -> show quantity inputs
        $('#facilities').on('change', function() {
            const selected = $(this).val();
            const container = $('#facilitiesQuantities');
            const qContainer = $('#quantityInputs');

            if (selected && selected.length > 0) {
                container.removeClass('d-none');
                let html = '';
                selected.forEach(function(id, idx) {
                    const fac = facilitiesData[id];
                    const oldQty = @json(old('quantities', []));
                    const qty = oldQty[idx] || 1;
                    html += `
                        <div class="facility-quantity">
                            <label>${fac.name}</label>
                            <input type="number" name="quantities[]" value="${qty}" min="1" max="100">
                        </div>
                    `;
                });
                qContainer.html(html);
            } else {
                container.addClass('d-none');
                qContainer.html('');
            }
        });

        // Trigger on load if old values exist
        @if(old('facilities'))
            $('#facilities').trigger('change');
        @endif

        // ============================================================
        // 2. ROOM CAPACITY HINT
        // ============================================================
        $('#room_id').on('change', function() {
            const selected = $(this).find('option:selected');
            const capacity = selected.data('capacity');
            const hint = $('#roomCapacityHint');
            if (capacity) {
                hint.show();
                $('#capacityDisplay').text(capacity);
            } else {
                hint.hide();
            }
            checkAvailability();
        });

        // ============================================================
        // 3. PURPOSE CHARACTER COUNTER
        // ============================================================
        $('#purpose').on('input', function() {
            const count = $(this).val().length;
            $('#purposeCount').text(count);
            if (count > 500) {
                $('#purposeCount').css('color', '#ef4444');
            } else {
                $('#purposeCount').css('color', 'var(--text-muted)');
            }
        });
        $('#purpose').trigger('input');

        // ============================================================
        // 4. AVAILABILITY CHECK - Real-time
        // ============================================================
        let checkTimeout;

        function checkAvailability() {
            const roomId = $('#room_id').val();
            const date = $('#start_date').val();
            const start = $('#start_time').val();
            const end = $('#end_time').val();

            const status = $('#availabilityStatus');
            const message = $('#availabilityMessage');
            const spinner = $('#availabilitySpinner');

            // Reset
            status.removeClass('visible available unavailable loading');

            if (!roomId || !date || !start || !end) {
                $('#submitBtn').prop('disabled', true);
                return;
            }

            // Combine date + time
            const startDateTime = date + 'T' + start + ':00';
            const endDateTime = date + 'T' + end + ':00';

            // Validate end > start
            if (new Date(endDateTime) <= new Date(startDateTime)) {
                status.addClass('visible unavailable');
                message.html('<i class="bi bi-exclamation-triangle"></i> Waktu selesai harus setelah waktu mulai');
                $('#submitBtn').prop('disabled', true);
                return;
            }

            // Validate working hours (07:00 - 16:00)
            const startHour = parseInt(start.split(':')[0]);
            const endHour = parseInt(end.split(':')[0]);
            if (startHour < 7 || endHour > 16 || startHour > 16 || endHour < 7) {
                status.addClass('visible unavailable');
                message.html('<i class="bi bi-exclamation-triangle"></i> Peminjaman hanya dapat dilakukan pada jam kerja 07:00 – 16:00 WIB');
                $('#submitBtn').prop('disabled', true);
                return;
            }

            // Validate 30-minute interval
            const startMinute = parseInt(start.split(':')[1]);
            const endMinute = parseInt(end.split(':')[1]);
            if (startMinute % 30 !== 0 || endMinute % 30 !== 0) {
                status.addClass('visible unavailable');
                message.html('<i class="bi bi-exclamation-triangle"></i> Waktu harus kelipatan 30 menit, contoh: 07:00, 07:30, 08:00');
                $('#submitBtn').prop('disabled', true);
                return;
            }

            // Show loading
            status.addClass('visible loading');
            spinner.show();
            message.html('Mengecek ketersediaan...');
            $('#submitBtn').prop('disabled', true);

            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(function() {
                $.ajax({
                    url: "{{ route('bookings.check-availability') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        room_id: roomId,
                        start_time: startDateTime,
                        end_time: endDateTime
                    },
                    success: function(res) {
                        status.removeClass('loading');
                        spinner.hide();
                        if (res.available) {
                            status.addClass('visible available');
                            message.html('<i class="bi bi-check-circle"></i> ' + res.message);
                            $('#submitBtn').prop('disabled', false);
                            // Show recommended slots
                            showRecommendedSlots(roomId, date);
                        } else {
                            status.addClass('visible unavailable');
                            message.html('<i class="bi bi-exclamation-triangle"></i> ' + res.message);
                            $('#submitBtn').prop('disabled', true);
                            $('#recommendedSlotsContainer').hide();
                        }
                    },
                    error: function(xhr) {
                        status.removeClass('loading');
                        spinner.hide();
                        status.addClass('visible unavailable');
                        let errorMsg = 'Gagal mengecek ketersediaan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        message.html('<i class="bi bi-exclamation-triangle"></i> ' + errorMsg);
                        $('#submitBtn').prop('disabled', true);
                    }
                });
            }, 400);
        }

        // ============================================================
        // 5. RECOMMENDED SLOTS
        // ============================================================
        function showRecommendedSlots(roomId, date) {
            const container = $('#recommendedSlotsContainer');
            const slotsContainer = $('#recommendedSlots');

            $.ajax({
                url: "{{ route('bookings.check-availability') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    room_id: roomId,
                    date_only: date,
                    get_slots: true
                },
                success: function(res) {
                    if (res.available_slots && res.available_slots.length > 0) {
                        container.show();
                        slotsContainer.html('');
                        res.available_slots.slice(0, 6).forEach(function(slot) {
                            const isSelected = slot === $('#start_time').val();
                            slotsContainer.append(`
                                <button type="button" class="slot-btn ${isSelected ? 'selected' : ''}"
                                        onclick="selectSlot('${slot}')"
                                        style="${isSelected ? 'background:rgba(16,185,129,0.12); border-color:var(--brand-green); color:var(--brand-green-dark);' : ''}">
                                    ${isSelected ? '<i class="bi bi-check-circle"></i>' : ''} ${slot}
                                </button>
                            `);
                        });
                    } else {
                        container.hide();
                    }
                },
                error: function() {
                    container.hide();
                }
            });
        }

        window.selectSlot = function(time) {
            $('#start_time').val(time);
            // Auto set end time +1 jam (dalam 30 menit)
            const parts = time.split(':');
            let hour = parseInt(parts[0]) + 1;
            const min = parts[1];
            if (hour >= 24) hour = 23;
            const endHour = String(hour).padStart(2, '0');
            // Cek apakah end time kelipatan 30 menit
            let endMin = min;
            if (parseInt(min) < 30) {
                endMin = '30';
            } else {
                endMin = '00';
                hour = parseInt(hour) + 1;
                const endHour2 = String(hour).padStart(2, '0');
                $('#end_time').val(endHour2 + ':' + endMin);
                checkAvailability();
                return;
            }
            $('#end_time').val(endHour + ':' + endMin);
            checkAvailability();
        };

        // ============================================================
        // 6. EVENT LISTENERS
        // ============================================================
        $('#room_id, #start_date, #start_time, #end_time').on('change', checkAvailability);
        $('#start_time, #end_time').on('change', function() {
            const start = $('#start_time').val();
            const end = $('#end_time').val();
            if (start && end) {
                const startMinutes = parseInt(start.split(':')[0]) * 60 + parseInt(start.split(':')[1]);
                const endMinutes = parseInt(end.split(':')[0]) * 60 + parseInt(end.split(':')[1]);
                if (endMinutes <= startMinutes) {
                    $('#end_time')[0].setCustomValidity('Waktu selesai harus setelah waktu mulai');
                } else {
                    $('#end_time')[0].setCustomValidity('');
                }
            }
        });

        // ============================================================
        // 7. FORM SUBMIT VALIDATION
        // ============================================================
        $('#bookingForm').on('submit', function(e) {
            const startDate = $('#start_date').val();
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();
            if (!startDate || !startTime || !endTime) {
                e.preventDefault();
                alert('Silakan isi semua field waktu');
                return false;
            }
            const startDateTime = new Date(startDate + 'T' + startTime + ':00');
            const endDateTime = new Date(startDate + 'T' + endTime + ':00');
            if (endDateTime <= startDateTime) {
                e.preventDefault();
                alert('Waktu selesai harus setelah waktu mulai');
                return false;
            }
        });

        // ============================================================
        // 8. INITIAL CHECK
        // ============================================================
        setTimeout(checkAvailability, 500);

    });
</script>
@endsection