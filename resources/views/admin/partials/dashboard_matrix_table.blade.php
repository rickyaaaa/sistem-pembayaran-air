<style>
/* Custom Pagination UI untuk Desktop & Mobile */
.matrix-pagination .pagination {
    flex-wrap: wrap;
    justify-content: center;
    gap: 4px;
    margin-bottom: 0;
}
.matrix-pagination .page-item .page-link {
    border-radius: 8px !important;
    padding: 0.4rem 0.75rem;
    font-size: 0.85rem;
    color: #1d4ed8;
    border: 1px solid rgba(0,0,0,0.08);
    font-weight: 500;
    transition: all 0.2s;
    background: #fff;
}
.matrix-pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #1d4ed8 0%, #0891b2 100%);
    color: white;
    border: none;
    box-shadow: 0 4px 10px rgba(29, 78, 216, 0.2);
}
.matrix-pagination .page-item.disabled .page-link {
    background: #f8fafc;
    color: #94a3b8;
    border-color: #e2e8f0;
}

/* Memaksa nomor halaman tetap muncul di mobile (menimpa default d-none Laravel di layar kecil) */
.matrix-pagination .page-item.d-none {
    display: block !important;
}

@media (max-width: 576px) {
    .matrix-pagination .page-item .page-link {
        padding: 0.35rem 0.6rem;
        font-size: 0.8rem;
    }
}
</style>

<div class="table-responsive">
    <table class="table table-sm mb-0">
        <thead>
            <tr>
                <th class="ps-3">Blok</th>
                @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $mn)
                    <th class="text-end">{{ $mn }}</th>
                @endforeach
                <th class="text-end pe-3">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($blockMonthlyIncome as $blok => $months)
                <tr>
                    <td class="fw-semibold ps-3">{{ $blok }}</td>
                    @for($m = 1; $m <= 12; $m++)
                        <td class="text-end" style="font-size:.8rem;">
                            {{ $months[$m] > 0 ? number_format($months[$m]/1000, 0).'k' : '-' }}
                        </td>
                    @endfor
                    <td class="text-end fw-semibold pe-3 text-primary">
                        Rp {{ number_format(array_sum($months), 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if(method_exists($blockMonthlyIncome, 'links'))
    <div class="mt-4 mb-3 px-3 d-flex justify-content-center matrix-pagination">
        {{ $blockMonthlyIncome->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
@endif
