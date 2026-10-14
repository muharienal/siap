{{-- QR PRESENSI + DAFTAR HADIR — digabung jadi SATU card di kolom
     utama (di bawah Informasi Peminjaman), bukan lagi dipisah antara
     kolom samping (QR) dan full-width terpisah (Daftar Hadir) seperti
     sebelumnya.

     FIX: pembungkus id="liveQrAttendance" SENGAJA selalu dirender (walau
     isinya kosong saat booking masih Pending) — kalau cuma dirender saat
     status==1 saja, ketika booking di-approve dari device lain, live-
     update tidak akan punya elemen untuk ditempeli QR yang baru muncul
     (tidak ada target buat ditukar), jadi QR-nya tidak akan pernah
     muncul otomatis tanpa reload manual. --}}
<div id="liveQrAttendance">
    @if($booking->status == 1 && $booking->absent_code)
    <div class="detail-card">
        <div class="card-section">
            <div class="section-title">
                <i class="bi bi-qr-code"></i> Kode Presensi
            </div>
            <div class="qr-section">
                <p class="text-muted small mb-2">Tunjukkan QR ini kepada peserta rapat untuk presensi</p>
                <div class="qr-code" id="qrCodeContainer"></div>
                <div class="qr-label">Kode: {{ $booking->absent_code }}</div>
                <button type="button" class="btn-outline-sm mt-3" style="display:inline-flex;" data-bs-toggle="modal" data-bs-target="#printQrModal">
                    <i class="bi bi-printer"></i> Cetak QR
                </button>
            </div>
        </div>

        @if($booking->attendances->count() > 0)
        <div class="card-section">
            <div class="section-title" style="justify-content: space-between; margin-bottom: var(--space-4);">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-people"></i> Daftar Hadir ({{ $booking->attendances->count() }})
                </span>
                <a href="{{ route('bookings.attendance.export', $booking->id) }}" class="btn-outline-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>
            <div class="facility-list-detail">
                @foreach($booking->attendances as $att)
                    <div class="facility-detail-item">
                        <div>
                            {{-- FIX: sebelumnya nama AKUN yang sedang login (user->full_name)
                                 menimpa nama yang benar-benar diketik di form presensi
                                 (guest_name) — jadi kalau admin scan QR sambil login lalu
                                 mengisi nama tamu, yang tampil malah "Administrator Sistem",
                                 bukan nama tamu yang diketik. guest_name selalu wajib diisi
                                 di form, jadi itu yang seharusnya jadi sumber tampilan utama;
                                 user_id cuma metadata pelacakan siapa yang login saat submit,
                                 bukan pengganti nama yang diinput. --}}
                            <div class="facility-detail-name">{{ $att->guest_name ?: ($att->user->full_name ?? '-') }}</div>
                            <small class="text-muted">{{ $att->guest_institution ?? 'Internal' }}</small>
                        </div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($att->check_in_time)->format('H:i') }}</small>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif
</div>