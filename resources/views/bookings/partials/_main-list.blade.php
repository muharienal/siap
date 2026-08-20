<div id="{{ $prefix }}ResultsArea">
@if($showBulk ?? false)
<div class="bulk-action-bar" id="bulkActionBar">
    <div class="selected-count">
        <i class="bi bi-check2-square"></i> <span id="selectedCount">0</span> booking dipilih
    </div>
    <div class="actions">
        <button type="button" class="bulk-btn" onclick="bulkAction('approve')">
            <i class="bi bi-check-circle"></i> Setujui Semua
        </button>
        <button type="button" class="bulk-btn danger" onclick="bulkAction('reject')">
            <i class="bi bi-x-circle"></i> Tolak Semua
        </button>
        <button type="button" class="bulk-btn" onclick="clearSelection()">
            <i class="bi bi-x"></i> Batal
        </button>
    </div>
</div>
@endif

<div class="booking-list-wrapper">
    @if($bookings->isEmpty())
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>Tidak ada peminjaman ditemukan</h4>
            <p>@if(request()->hasAny(["{$prefix}_room", "{$prefix}_date", "{$prefix}_date_from", "{$prefix}_date_to", "{$prefix}_start_time", "{$prefix}_end_time"])) Coba ubah filter pencarian Anda @else Belum ada peminjaman dengan status ini @endif</p>
        </div>
    @else
        {{-- Desktop Table --}}
        <div class="booking-table-wrap">
            <table class="booking-table" id="{{ $prefix }}BookingTable">
                <thead>
                    <tr>
                        @if($showBulk ?? false)
                        <th class="checkbox-cell">
                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll(this)">
                        </th>
                        @endif
                        <th>Ruangan</th>
                        <th>Tanggal & Waktu</th>
                        <th>Peminjam</th>
                        @if(Auth::user()->role == 1)
                        <th>Jenis</th>
                        @endif
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        @php
                            $statusClass = $booking->status == 0 ? 'pending' : ($booking->status == 1 ? 'approved' : 'rejected');
                            $statusLabel = $booking->status == 0 ? 'Pending' : ($booking->status == 1 ? 'Disetujui' : 'Ditolak');
                            $typeClass = $booking->booking_type == 'Priority' ? 'priority' : 'regular';
                            $typeLabel = $booking->booking_type == 'Priority' ? 'Priority' : 'Reguler';
                            $roomPhoto = $booking->room->photos->isNotEmpty() ? $booking->room->photos->first()->photo_url : null;

                            $bookingDate = \Carbon\Carbon::parse($booking->start_time)->startOfDay();
                            $todayDate = \Carbon\Carbon::today();
                            $diffDays = $todayDate->diffInDays($bookingDate);
                            if ($bookingDate->lt($todayDate)) $diffDays = -$diffDays;

                            if ($diffDays === 0) {
                                $dayBadge = ['label' => 'Hari Ini', 'class' => 'today'];
                            } elseif ($diffDays === 1) {
                                $dayBadge = ['label' => 'Besok', 'class' => 'soon'];
                            } elseif ($diffDays === 2) {
                                $dayBadge = ['label' => 'Lusa', 'class' => 'soon'];
                            } elseif ($diffDays > 2 && $diffDays <= 14) {
                                $dayBadge = ['label' => $diffDays . ' Hari Lagi', 'class' => 'upcoming'];
                            } elseif ($diffDays < 0) {
                                $dayBadge = ['label' => abs($diffDays) . ' Hari Lalu', 'class' => 'past'];
                            } else {
                                $dayBadge = null;
                            }
                        @endphp
                        <tr class="booking-row" data-id="{{ $booking->id }}">
                            @if($showBulk ?? false)
                            <td class="checkbox-cell">
                                {{-- FIX: sebelumnya cuma cek status!=0 — booking yang sedang
                                     menunggu KONFIRMASI PEMINJAM (hasil pengalihan admin)
                                     statusnya TETAP 0 (Pending), jadi checkbox-nya ikut aktif
                                     dan bisa ke-bulk-approve/tolak oleh admin — padahal
                                     keputusan itu seharusnya cuma boleh diambil peminjam
                                     sendiri lewat tombol Setuju/Tolak Pengalihan miliknya. --}}
                                <input type="checkbox" class="form-check-input booking-checkbox" value="{{ $booking->id }}" onchange="updateBulkBar()" {{ ($booking->status != 0 || $booking->awaiting_user_confirmation) ? 'disabled' : '' }}>
                            </td>
                            @endif
                            <td class="room-cell">
                                <div class="room-info-cell">
                                    <div class="room-thumbnail">
                                        @if($roomPhoto)
                                            <img src="{{ $roomPhoto }}" alt="{{ $booking->room->name }}">
                                        @else
                                            <i class="bi bi-door-open"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div>{{ $booking->room->name }}</div>
                                        <small class="text-muted">Kapasitas {{ $booking->room->capacity }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($dayBadge)
                                    <span class="day-badge {{ $dayBadge['class'] }}">{{ $dayBadge['label'] }}</span><br>
                                @endif
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMM Y') }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </small>
                            </td>
                            <td>
                                <div>{{ $booking->user->full_name ?? 'Unknown' }}</div>
                                <small class="text-muted">{{ $booking->user->nip ?? '-' }}</small>
                            </td>
                            @if(Auth::user()->role == 1)
                            <td>
                                <span class="type-badge type-{{ $typeClass }}">
                                    <i class="bi bi-{{ $booking->booking_type == 'Priority' ? 'star-fill' : 'circle' }}"></i>
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            @endif
                            <td class="purpose-cell" title="{{ $booking->purpose }}">{{ $booking->purpose }}</td>
                            <td>
                                <span class="status-badge status-{{ $statusClass }}">{{ $statusLabel }}</span>
                                @if($booking->displaced_by_id && Auth::user()->role == 1)
                                    <br>
                                    <span class="conflict-badge">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Bentrok
                                    </span>
                                @endif
                            </td>
                            @php
                                $noteText = $booking->status == 2
                                    ? $booking->rejection_reason
                                    : ($booking->approval_note ?? $booking->relocation_note);
                            @endphp
                            <td class="reason-cell" data-reason="{{ $noteText ?? '-' }}">
                                {{ $noteText ? Str::limit($noteText, 30) : '-' }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="action-btn view" title="Pratinjau">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="booking-cards">
            @foreach($bookings as $booking)
                @php
                    $statusClass = $booking->status == 0 ? 'pending' : ($booking->status == 1 ? 'approved' : 'rejected');
                    $statusLabel = $booking->status == 0 ? 'Pending' : ($booking->status == 1 ? 'Disetujui' : 'Ditolak');
                    $typeClass = $booking->booking_type == 'Priority' ? 'priority' : 'regular';
                    $typeLabel = $booking->booking_type == 'Priority' ? 'Priority' : 'Reguler';
                    $roomPhoto = $booking->room->photos->isNotEmpty() ? $booking->room->photos->first()->photo_url : null;

                    $bookingDate = \Carbon\Carbon::parse($booking->start_time)->startOfDay();
                    $todayDate = \Carbon\Carbon::today();
                    $diffDays = $todayDate->diffInDays($bookingDate);
                    if ($bookingDate->lt($todayDate)) $diffDays = -$diffDays;

                    if ($diffDays === 0) {
                        $dayBadge = ['label' => 'Hari Ini', 'class' => 'today'];
                    } elseif ($diffDays === 1) {
                        $dayBadge = ['label' => 'Besok', 'class' => 'soon'];
                    } elseif ($diffDays === 2) {
                        $dayBadge = ['label' => 'Lusa', 'class' => 'soon'];
                    } elseif ($diffDays > 2 && $diffDays <= 14) {
                        $dayBadge = ['label' => $diffDays . ' Hari Lagi', 'class' => 'upcoming'];
                    } elseif ($diffDays < 0) {
                        $dayBadge = ['label' => abs($diffDays) . ' Hari Lalu', 'class' => 'past'];
                    } else {
                        $dayBadge = null;
                    }
                @endphp
                <div class="booking-card status-{{ $statusClass }}">
                    <div class="booking-card-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="room-thumbnail">
                                @if($roomPhoto)
                                    <img src="{{ $roomPhoto }}" alt="{{ $booking->room->name }}">
                                @else
                                    <i class="bi bi-door-open"></i>
                                @endif
                            </div>
                            <div>
                                <div class="booking-card-title">{{ $booking->room->name }}</div>
                                @if($dayBadge)
                                    <span class="day-badge {{ $dayBadge['class'] }}">{{ $dayBadge['label'] }}</span>
                                @endif
                                <div class="booking-card-time">
                                    <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMM Y') }}
                                    <i class="bi bi-clock ms-2"></i> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="status-badge status-{{ $statusClass }}">{{ $statusLabel }}</span>
                            @if($booking->displaced_by_id && Auth::user()->role == 1)
                                <span class="conflict-badge" style="margin-top:0;">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Bentrok
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="booking-card-body">
                        <div class="booking-card-meta">
                            @if(Auth::user()->role == 1)
                            <span class="type-badge type-{{ $typeClass }}">
                                <i class="bi bi-{{ $booking->booking_type == 'Priority' ? 'star-fill' : 'circle' }}"></i>
                                {{ $typeLabel }}
                            </span>
                            @endif
                            <small class="text-muted">
                                <i class="bi bi-person"></i> {{ $booking->user->full_name ?? 'Unknown' }}
                            </small>
                        </div>
                        <div class="mb-1"><strong>Tujuan:</strong> {{ Str::limit($booking->purpose, 80) }}</div>
                        @if($booking->status == 2 && $booking->rejection_reason)
                        <div class="text-danger small mt-2">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Alasan Ditolak:</strong> {{ $booking->rejection_reason }}
                        </div>
                        @elseif($booking->status == 1 && ($booking->approval_note || $booking->relocation_note))
                        <div class="text-success small mt-2">
                            <i class="bi bi-check-circle"></i>
                            <strong>Catatan Admin:</strong> {{ $booking->approval_note ?? $booking->relocation_note }}
                        </div>
                        @endif
                    </div>
                    <div class="booking-card-actions">
                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="bi bi-eye"></i> Pratinjau
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@if($bookings->hasPages())
<div class="d-flex justify-content-center mt-5">
    {{ $bookings->appends(request()->query())->links() }}
</div>
@endif
</div>