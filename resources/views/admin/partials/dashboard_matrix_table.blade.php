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

@if(method_exists($blockMonthlyIncome, 'links'))
    <div class="mt-3 px-3">
        {{ $blockMonthlyIncome->links() }}
    </div>
@endif
