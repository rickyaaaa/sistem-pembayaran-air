@if($status instanceof \App\Enums\BillStatus || $status instanceof \App\Enums\PaymentStatus)
    <span class="badge {{ $status->badgeClass() }}">{{ $status->label() }}</span>
@else
    <span class="badge bg-secondary">{{ (string) $status }}</span>
@endif