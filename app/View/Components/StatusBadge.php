<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatusBadge extends Component
{
    /**
     * @param mixed $status The status enum object (BillStatus or PaymentStatus)
     */
    public function __construct(public mixed $status)
    {
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
@if($status instanceof \App\Enums\BillStatus || $status instanceof \App\Enums\PaymentStatus)
    <span class="badge {{ $status->badgeClass() }}">
        {{ $status->label() }}
    </span>
@else
    <span class="badge bg-secondary">{{ (string) $status }}</span>
@endif
BLADE;
    }
}
