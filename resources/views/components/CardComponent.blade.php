<div class="col-md-6 col-xl-6">
    <div class="card">
        <div class="card-body">
            <div class="d-flex">
                <div class="flex-grow-1">
                    <span class="text-muted text-uppercase fs-12 fw-bold">{{ $title }}</span>
                    <h3 class="mb-0">Rp. {{ number_format($amount, '0', '.', ',') }}</h3>
                    <small>Dengan total {{ $count }}x pemesanan</small>
                </div>
                <div class="align-self-center flex-shrink-0">
                    <span class="icon-lg icon-dual-{{ $color }}" data-feather="{{ $icon }}"></span>
                </div>
            </div>
        </div>
    </div>
</div>
