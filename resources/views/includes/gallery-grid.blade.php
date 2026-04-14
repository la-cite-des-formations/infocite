{{-- Grille dynamique de la galerie photos --}}
{{-- Layouts adaptatifs selon le nombre de photos --}}
@if ($gallery && $gallery->imagesCount() > 0)
    @php
        $images = $gallery->sortedImages();
        $count = $images->count();
    @endphp

    <div class="gallery-container mt-4 mb-3">
        <div class="gallery-title text-muted small text-uppercase fw-bold mb-2 text-center">
            <i class="bx bx-images me-1"></i>Galerie Photos
        </div>

        <div class="gallery-grid" data-gallery-id="{{ $gallery->id }}">
            {{-- BLOC MOSAÏQUE (Mobile/Tablette) --}}
            <div class="gallery-mosaic d-md-none">
                {{-- Layout 1 photo : pleine largeur --}}
                @if ($count === 1)
                    <div class="gallery-layout gallery-layout-1">
                        <a href="{{ $images[0]['path'] }}" class="glightbox gallery-item"
                            data-gallery="gallery-{{ $gallery->id }}-mosaic">
                            <img src="{{ $images[0]['path'] }}" alt="{{ $images[0]['filename'] }}" loading="lazy">
                        </a>
                    </div>
                    {{-- Mosaïques 2, 3, 4 et 5+ (Logique existante) --}}
                @elseif ($count === 2)
                    <div class="gallery-layout gallery-layout-2">
                        @foreach ($images as $i => $image)
                            <a href="{{ $image['path'] }}" class="glightbox gallery-item"
                                data-gallery="gallery-{{ $gallery->id }}-mosaic">
                                <img src="{{ $image['path'] }}" alt="{{ $image['filename'] }}" loading="lazy">
                            </a>
                        @endforeach
                    </div>
                @elseif ($count === 3)
                    <div class="gallery-layout gallery-layout-3">
                        <a href="{{ $images[0]['path'] }}" class="glightbox gallery-item gallery-item-main"
                            data-gallery="gallery-{{ $gallery->id }}-mosaic">
                            <img src="{{ $images[0]['path'] }}" alt="{{ $images[0]['filename'] }}" loading="lazy">
                        </a>
                        <div class="gallery-item-side">
                            @foreach ($images->slice(1) as $index => $image)
                                <a href="{{ $image['path'] }}" class="glightbox gallery-item"
                                    data-gallery="gallery-{{ $gallery->id }}-mosaic">
                                    <img src="{{ $image['path'] }}" alt="{{ $image['filename'] }}" loading="lazy">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="gallery-layout gallery-layout-4">
                        @foreach ($images->take(3) as $i => $image)
                            <a href="{{ $image['path'] }}" class="glightbox gallery-item"
                                data-gallery="gallery-{{ $gallery->id }}-mosaic">
                                <img src="{{ $image['path'] }}" alt="{{ $image['filename'] }}" loading="lazy">
                            </a>
                        @endforeach
                        <a href="{{ $images[3]['path'] }}" class="glightbox gallery-item gallery-item-more"
                            data-gallery="gallery-{{ $gallery->id }}-mosaic">
                            <img src="{{ $images[3]['path'] }}" alt="{{ $images[3]['filename'] }}" loading="lazy">
                            <div class="gallery-overlay">
                                <span>+ {{ $count - 3 }}</span>
                            </div>
                        </a>
                        {{-- Images cachées pour la mosaïque --}}
                        <div class="d-none">
                            @foreach ($images->slice(4) as $image)
                                <a href="{{ $image['path'] }}" class="glightbox"
                                    data-gallery="gallery-{{ $gallery->id }}-mosaic"></a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- BLOC BANDE (Desktop) --}}
            <div class="gallery-strip d-none d-md-flex">
                @foreach ($images->take(min($count, 3)) as $i => $image)
                    <a href="{{ $image['path'] }}" class="glightbox gallery-item"
                        data-gallery="gallery-{{ $gallery->id }}-strip">
                        <img src="{{ $image['path'] }}" alt="{{ $image['filename'] }}" loading="lazy">
                    </a>
                @endforeach

                @if ($count >= 5)
                    <a href="{{ $images[3]['path'] }}" class="glightbox gallery-item gallery-item-more"
                        data-gallery="gallery-{{ $gallery->id }}-strip">
                        <img src="{{ $images[3]['path'] }}" alt="{{ $images[3]['filename'] }}" loading="lazy">
                        <div class="gallery-overlay">
                            <span>+ {{ $count - 3 }}</span>
                        </div>
                    </a>
                @elseif ($count === 4)
                    <a href="{{ $images[3]['path'] }}" class="glightbox gallery-item"
                        data-gallery="gallery-{{ $gallery->id }}-strip">
                        <img src="{{ $images[3]['path'] }}" alt="{{ $images[3]['filename'] }}" loading="lazy">
                    </a>
                @endif

                {{-- Images cachées pour la bande --}}
                <div class="d-none">
                    @foreach ($images->slice(4) as $image)
                        <a href="{{ $image['path'] }}" class="glightbox"
                            data-gallery="gallery-{{ $gallery->id }}-strip"></a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @once
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const lightbox = GLightbox({
                    selector: '.glightbox',
                    touchNavigation: true,
                    loop: true,
                    autoplayVideos: true
                });
            });
        </script>
    @endonce
@endif
