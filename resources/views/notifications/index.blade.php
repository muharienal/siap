@extends('templates.template')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-bell me-2"></i>
                                Semua Notifikasi
                            </h4>
                            <div>
                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                                        </button>
                                    </form>
                                @endif
                                <span class="badge badge-info ml-2">{{ $unreadCount }} Belum Dibaca</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($notifications->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($notifications as $notification)
                                    <div class="list-group-item {{ !$notification->is_read ? 'bg-light border-left-primary' : '' }}">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                @if(!$notification->is_read)
                                                    <div class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>
                                                @endif
                                            </div>
                                            <div class="col-auto">
                                                <div class="notification-icon">
                                                    <i class="fas fa-bell text-primary fa-lg"></i>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <p class="mb-1">{{ $notification->message }}</p>
                                                        @if($notification->booking)
                                                            <small class="text-muted">
                                                                <i class="fas fa-door-open"></i>
                                                                Ruangan: {{ $notification->booking->room->name ?? 'N/A' }}
                                                                @if($notification->booking_id)
                                                                    | <a href="{{ route('bookings.show', $notification->booking_id) }}" 
                                                                         class="text-primary">Lihat Detail Booking</a>
                                                                @endif
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="text-right">
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock"></i>
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $notification->created_at->format('d M Y, H:i') }}
                                                        </small>
                                                        @if(!$notification->is_read)
                                                            <br>
                                                            <form method="POST" action="{{ route('notifications.mark-read') }}" class="d-inline mt-1">
                                                                @csrf
                                                                <input type="hidden" name="notification_id" value="{{ $notification->id }}">
                                                                <button type="submit" class="btn btn-outline-success btn-xs">
                                                                    <i class="fas fa-check"></i> Tandai Dibaca
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak Ada Notifikasi</h5>
                                <p class="text-muted">Anda belum memiliki notifikasi apapun.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-css')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 123, 255, 0.1);
        border-radius: 50%;
    }
    
    .list-group-item {
        transition: all 0.3s ease;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa !important;
    }
    
    .btn-xs {
        padding: 0.1rem 0.3rem;
        font-size: 0.75rem;
    }
</style>
@endpush