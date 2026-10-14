<div id="liveTopArea">
<div class="db-toolbar">
    <a href="{{ route('bookings.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div class="actions">
        @php
            // FIX: nilai-nilai ini SEHARUSNYA selalu dikirim dari controller
            // (show()/live()) supaya konsisten dengan bagian lain di
            // show.blade.php yang butuh variabel yang SAMA (partial yang
            // di-@include tidak "membocorkan" variabel @php-nya balik ke
            // file induk). Fallback di bawah ini cuma jaring pengaman kalau
            // suatu saat ada pemanggil lain yang lupa mengirimkannya —
            // bukan cara utama untuk menghitungnya.
            $isConflicted = $isConflicted ?? ((bool) $booking->displaced_by_id && $booking->status != 2);
            $awaitingConfirmation = $awaitingConfirmation ?? (bool) $booking->awaiting_user_confirmation;
            $isBookingOwner = $isBookingOwner ?? ($booking->user_id == Auth::id());
        @endphp

        @if($awaitingConfirmation && $isBookingOwner)
        <button type="button" class="btn-approve-sm" data-bs-toggle="modal" data-bs-target="#confirmRelocationModal">
            <i class="bi bi-check-lg"></i> Setuju Pengalihan
        </button>
        <button type="button" class="btn-danger-outline" data-bs-toggle="modal" data-bs-target="#declineRelocationModal">
            <i class="bi bi-x-lg"></i> Tolak Pengalihan
        </button>
        @endif

        @if(Auth::user()->role == 1 && $isConflicted)
        <button type="button" class="btn-primary-sm" data-bs-toggle="modal" data-bs-target="#relocateModal">
            <i class="bi bi-arrow-left-right"></i> Alihkan ke Ruangan Lain
        </button>
        @endif

        {{-- FIX: sebelumnya admin (role==1) SELALU lihat tombol Edit/Hapus
             berapa pun statusnya — bug ini bikin booking yang sudah
             diproses (disetujui/ditolak) masih bisa diubah/dihapus admin,
             padahal riwayat harusnya permanen begitu sudah diputuskan.
             Sekarang syarat "$booking->status == 0" berlaku untuk SEMUA
             role tanpa kecuali — bedanya cuma soal kepemilikan: admin
             boleh edit booking siapa saja (selama masih pending), non-admin
             cuma boleh edit booking miliknya sendiri (selama masih pending). --}}
        @if(!$isConflicted && !$awaitingConfirmation && $booking->status == 0 && (Auth::user()->role == 1 || $isBookingOwner))
        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn-outline-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endif

        @if(Auth::user()->role == 1 && !$awaitingConfirmation && $booking->status == 0)
            @if(!$isConflicted)
            <button type="button" class="btn-approve-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="bi bi-check-lg"></i> Setujui
            </button>
            @endif
            <button type="button" class="btn-danger-outline" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-lg"></i> Tolak
            </button>
        @endif

        {{-- FIX: booking yang SUDAH Disetujui sekarang bisa dibatalkan
             admin (statusnya jadi Ditolak, dengan alasan wajib diisi —
             tetap tercatat permanen di riwayat, bukan dihapus) — tapi
             TETAP TIDAK BISA DIHAPUS (lihat tombol Hapus di bawah, masih
             terkunci cuma untuk status Pending). Method reject() di
             controller sebenarnya SUDAH lama mendukung skenario ini
             (mewajibkan alasan pembatalan saat status==1), cuma
             tombolnya belum pernah ditampilkan untuk booking yang sudah
             disetujui — sekarang ditampilkan, dengan label yang beda
             ("Batalkan Persetujuan") supaya jelas bedanya dengan
             "Tolak" booking yang masih Pending. --}}
        @if(Auth::user()->role == 1 && !$awaitingConfirmation && $booking->status == 1)
        <button type="button" class="btn-danger-outline" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="bi bi-x-lg"></i> Batalkan Persetujuan
        </button>
        @endif

        {{-- FIX: tombol Hapus DIHAPUS SELURUHNYA dari sini — sebelumnya
             masih ada untuk booking berstatus Pending, tapi sekarang
             SEMUA booking (apa pun statusnya) harus tetap tersimpan
             sebagai record permanen, tidak ada yang boleh terhapus sama
             sekali. Endpoint bookings.destroy tetap ada di server tapi
             sekarang selalu menolak (lihat BookingController::destroy())
             — ini murni menyembunyikan tombolnya karena aksinya memang
             sudah tidak pernah bisa berhasil lagi. --}}
    </div>
</div>

{{-- HEADER: foto ruangan + nama + status --}}
<div class="detail-card" style="margin-bottom: var(--space-5);">
    <div class="card-section">
        <div class="booking-header">
            <div class="booking-header-main">
                @php
                    $bRoomPhotos = $booking->room->photos ?? collect();
                    $bRoomPhoto = $bRoomPhotos->count() > 0 ? $bRoomPhotos->first()->photo_url : null;
                @endphp
                <div class="booking-header-photo">
                    @if($bRoomPhoto)
                        <img src="{{ $bRoomPhoto }}" alt="{{ $booking->room->name }}">
                    @else
                        <div class="booking-header-photo-placeholder"><i class="bi bi-door-open"></i></div>
                    @endif
                </div>
                <div>
                    <div class="booking-room">{{ $booking->room->name }}</div>
                    <div class="booking-date">
                        <i class="bi bi-calendar3"></i>
                        {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </div>
                </div>
            </div>
            @php
                $statusClass = $booking->status_key; // pending|approved|rejected|cancelled
                $statusLabel = $booking->status_label;
            @endphp
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(Auth::user()->role == 1)
                    @if($booking->booking_type === 'Priority')
                        <span class="conflict-badge" style="background: rgba(139,92,246,0.12); color: #7c3aed;">
                            <i class="bi bi-star-fill"></i> Priority
                        </span>
                    @endif
                    @if($isConflicted)
                        <span class="conflict-badge">
                            <i class="bi bi-exclamation-triangle-fill"></i> Bentrok
                        </span>
                    @endif
                @endif
                @if($awaitingConfirmation)
                    <span class="conflict-badge" style="background: rgba(59,130,246,0.12); color: #1d4ed8;">
                        <i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi Peminjam
                    </span>
                @endif
                <span class="status-badge status-{{ $statusClass }}">
                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> {{ $statusLabel }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ALERT KONTEKSTUAL — full-width supaya langsung menarik perhatian,
     di luar grid 2 kolom di bawahnya. --}}

{{-- Menunggu konfirmasi peminjam: ini alert PALING PENTING kalau sedang
     aktif, jadi ditaruh paling atas. Tombol aksinya sudah ada di toolbar
     atas (Setuju Pengalihan/Tolak Pengalihan) — di sini cukup informasi,
     tidak perlu tombol dobel. --}}
@if($awaitingConfirmation)
<div class="detail-card" style="margin-bottom: var(--space-5);">
    <div class="card-section">
        <div class="conflict-alert" style="background: rgba(59,130,246,0.06); border-color: rgba(59,130,246,0.25);">
            <div class="conflict-alert-title" style="color: #1d4ed8;">
                <i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi Anda
            </div>
            <div class="conflict-alert-text">
                Admin mengusulkan pengalihan ke <strong>{{ $booking->room->name }}</strong>,
                {{ $booking->start_time->locale('id')->isoFormat('dddd, D MMMM Y, HH:mm') }}&ndash;{{ $booking->end_time->format('H:i') }} WIB.
                @if($booking->relocation_note)
                    Catatan admin: <em>"{{ $booking->relocation_note }}"</em>.
                @endif
                @if($isBookingOwner)
                    Setujui kalau Anda bisa hadir di ruangan/jam baru ini, atau tolak lewat tombol di atas.
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- Alert konflik jadwal: admin melihat detail lengkap (siapa, ruangan
     prioritas mana). Selain admin — termasuk pemilik booking ini sendiri
     — hanya melihat versi netral tanpa kata "prioritas" sama sekali,
     supaya tidak ada yang tahu ada sistem Priority/Reguler. --}}
@if($isConflicted)
<div class="detail-card" style="margin-bottom: var(--space-5);">
    <div class="card-section">
        <div class="conflict-alert">
            @if(Auth::user()->role == 1 && $booking->displacedBy)
                <div class="conflict-alert-title">
                    <i class="bi bi-exclamation-triangle-fill"></i> Jadwal Bentrok
                </div>
                <div class="conflict-alert-text">
                    Bentrok dengan peminjaman prioritas <strong>{{ $booking->displacedBy->user->full_name ?? 'Direksi' }}</strong>
                    (<a href="{{ route('bookings.show', $booking->displacedBy->id) }}">lihat detail &rarr;</a>).
                    Alihkan lewat tombol "Alihkan ke Ruangan Lain" di atas.
                </div>
            @else
                <div class="conflict-alert-title">
                    <i class="bi bi-info-circle-fill"></i> Sedang Ditinjau Admin
                </div>
                <div class="conflict-alert-text">
                    Jadwal ini bentrok dengan peminjaman lain. Admin akan segera mengalihkannya ke ruangan atau waktu lain.
                </div>
            @endif
        </div>
    </div>
</div>
@endif

@if(Auth::user()->role == 1 && $booking->displaces->count() > 0)
<div class="detail-card" style="margin-bottom: var(--space-5);">
    <div class="card-section">
        <div class="displaces-info">
            <div class="displaces-info-title">
                <i class="bi bi-info-circle-fill"></i> Dampak Prioritas
            </div>
            <div class="displaces-info-text">
                Bentrok dengan {{ $booking->displaces->count() }} peminjaman reguler berikut. Alihkan jadwalnya ke ruangan atau waktu lain.
            </div>
            <div class="facility-list-detail">
                @foreach($booking->displaces as $d)
                    <div class="facility-detail-item">
                        <div class="facility-detail-name">{{ $d->user->full_name ?? 'Unknown' }}</div>
                        <a href="{{ route('bookings.show', $d->id) }}" class="text-decoration-none small fw-semibold" style="color: var(--brand-blue);">
                            {{ $d->displaced_by_id ? 'Belum dialihkan' : 'Sudah dialihkan' }} &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

@if($booking->status == 2 && $booking->rejection_reason)
<div class="detail-card" style="margin-bottom: var(--space-5);">
    <div class="card-section">
        <div class="rejection-alert">
            <div class="rejection-alert-title">
                <i class="bi bi-exclamation-triangle"></i> {{ $booking->was_cancelled_approval ? 'Alasan Pembatalan' : 'Alasan Penolakan' }}
            </div>
            <div class="rejection-alert-text">{{ $booking->rejection_reason }}</div>
        </div>
    </div>
</div>
@endif
</div>