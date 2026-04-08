<div>
    <div class="post-rating mt-3 p-3 bg-light rounded border shadow-sm">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small text-uppercase fw-bold mb-1">
                    <i class="bx bx-medal me-1"></i>Notez cet article
                </div>
                <div class="stars fs-2 d-flex gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <i wire:click="setRating({{ $i }})" @class([
                            'bx cursor-pointer transition-all',
                            'bxs-star text-warning' => $i <= $rating,
                            'bx-star text-secondary' => $i > $rating,
                        ])
                            style="cursor: pointer; transition: all 0.2s ease;" title="Noter {{ $i }} sur 5"
                            onmouseover="this.classList.replace('bx-star', 'bxs-star'); this.classList.add('text-warning'); this.style.transform='scale(1.2)'"
                            onmouseout="if({{ $i }} > {{ $rating }}) { this.classList.replace('bxs-star', 'bx-star'); this.classList.remove('text-warning'); } this.style.transform='scale(1)'">
                        </i>
                    @endfor
                </div>
            </div>

            @php $avg = $post->averageRating(); @endphp
            @if ($avg > 0)
                <div class="text-end border-start ps-4">
                    <div class="average-rating d-flex flex-column align-items-center">
                        <div class="d-flex align-items-baseline">
                            <span class="fs-3 fw-bold text-primary">{{ number_format($avg, 1) }}</span>
                            <span class="text-muted ms-1">/ 5</span>
                        </div>
                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                            Moyenne ({{ $post->readers()->wherePivot('rating', '>', 0)->count() }} avis)
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
