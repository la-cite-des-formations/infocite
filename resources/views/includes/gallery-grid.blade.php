{{-- Grille dynamique de la galerie photos & vidéos --}}
{{-- Layouts adaptatifs selon le nombre de médias --}}
@if ($gallery && $gallery->imagesCount() > 0)
    @php
        $images = $gallery->sortedImages();
        $count = $images->count();
        $hasVideos = $images->contains(fn($i) => ($i['type'] ?? 'image') === 'video');
    @endphp

    <div class="gallery-container mt-4 mb-3">
        <div class="gallery-title text-muted small text-uppercase fw-bold mb-2 text-center">
            <i class="bx bx-images me-1"></i>Galerie multimédia
        </div>

        <div class="gallery-grid" data-gallery-id="{{ $gallery->id }}">
            {{-- BLOC MOSAÏQUE (Mobile/Tablette) --}}
            <div class="gallery-mosaic d-md-none">
                {{-- Layout 1 média : pleine largeur --}}
                @if ($count === 1)
                    @php
                        $img0 = $images[0];
                        $isVid0 = ($img0['type'] ?? 'image') === 'video';
                        $dataType0 = $isVid0 ? (($img0['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                    @endphp
                    <div class="gallery-layout gallery-layout-1">
                        <a href="{{ $img0['path'] }}" class="glightbox gallery-item" {!! $dataType0 !!}
                            data-gallery="gallery-{{ $gallery->id }}-mosaic">
                            <img src="{{ $img0['thumbnail'] ?? $img0['path'] }}" alt="{{ $img0['filename'] }}" loading="lazy">
                            @if ($isVid0)
                                <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                            @endif
                        </a>
                    </div>
                @elseif ($count === 2)
                    <div class="gallery-layout gallery-layout-2">
                        @foreach ($images as $i => $image)
                            @php
                                $isVid = ($image['type'] ?? 'image') === 'video';
                                $dataType = $isVid ? (($image['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                            @endphp
                            <a href="{{ $image['path'] }}" class="glightbox gallery-item" {!! $dataType !!}
                                data-gallery="gallery-{{ $gallery->id }}-mosaic">
                                <img src="{{ $image['thumbnail'] ?? $image['path'] }}" alt="{{ $image['filename'] }}" loading="lazy">
                                @if ($isVid)
                                    <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @elseif ($count === 3)
                    @php
                        $mainImg = $images[0];
                        $isVidMain = ($mainImg['type'] ?? 'image') === 'video';
                        $dataTypeMain = $isVidMain ? (($mainImg['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                    @endphp
                    <div class="gallery-layout gallery-layout-3">
                        <a href="{{ $mainImg['path'] }}" class="glightbox gallery-item gallery-item-main" {!! $dataTypeMain !!}
                            data-gallery="gallery-{{ $gallery->id }}-mosaic">
                            <img src="{{ $mainImg['thumbnail'] ?? $mainImg['path'] }}" alt="{{ $mainImg['filename'] }}" loading="lazy">
                            @if ($isVidMain)
                                <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                            @endif
                        </a>
                        <div class="gallery-item-side">
                            @foreach ($images->slice(1) as $index => $image)
                                @php
                                    $isVid = ($image['type'] ?? 'image') === 'video';
                                    $dataType = $isVid ? (($image['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                                @endphp
                                <a href="{{ $image['path'] }}" class="glightbox gallery-item" {!! $dataType !!}
                                    data-gallery="gallery-{{ $gallery->id }}-mosaic">
                                    <img src="{{ $image['thumbnail'] ?? $image['path'] }}" alt="{{ $image['filename'] }}" loading="lazy">
                                    @if ($isVid)
                                        <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="gallery-layout gallery-layout-4">
                        @foreach ($images->take(3) as $i => $image)
                            @php
                                $isVid = ($image['type'] ?? 'image') === 'video';
                                $dataType = $isVid ? (($image['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                            @endphp
                            <a href="{{ $image['path'] }}" class="glightbox gallery-item" {!! $dataType !!}
                                data-gallery="gallery-{{ $gallery->id }}-mosaic">
                                <img src="{{ $image['thumbnail'] ?? $image['path'] }}" alt="{{ $image['filename'] }}" loading="lazy">
                                @if ($isVid)
                                    <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                                @endif
                            </a>
                        @endforeach
                        @php
                            $img3 = $images[3];
                            $isVid3 = ($img3['type'] ?? 'image') === 'video';
                            $dataType3 = $isVid3 ? (($img3['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                        @endphp
                        <a href="{{ $img3['path'] }}" class="glightbox gallery-item gallery-item-more" {!! $dataType3 !!}
                            data-gallery="gallery-{{ $gallery->id }}-mosaic">
                            <img src="{{ $img3['thumbnail'] ?? $img3['path'] }}" alt="{{ $img3['filename'] }}" loading="lazy">
                            @if ($isVid3)
                                <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                            @endif
                            <div class="gallery-overlay">
                                <span>+ {{ $count - 3 }}</span>
                            </div>
                        </a>
                        {{-- Médias cachés pour la mosaïque --}}
                        <div class="d-none">
                            @foreach ($images->slice(4) as $image)
                                @php
                                    $isVid = ($image['type'] ?? 'image') === 'video';
                                    $dataType = $isVid ? (($image['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                                @endphp
                                <a href="{{ $image['path'] }}" class="glightbox" {!! $dataType !!}
                                    data-gallery="gallery-{{ $gallery->id }}-mosaic"></a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- BLOC BANDE (Desktop) --}}
            <div class="gallery-strip d-none d-md-flex">
                @foreach ($images->take(min($count, 3)) as $i => $image)
                    @php
                        $isVid = ($image['type'] ?? 'image') === 'video';
                        $dataType = $isVid ? (($image['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                    @endphp
                    <a href="{{ $image['path'] }}" class="glightbox gallery-item" {!! $dataType !!}
                        data-gallery="gallery-{{ $gallery->id }}-strip">
                        <img src="{{ $image['thumbnail'] ?? $image['path'] }}" alt="{{ $image['filename'] }}" loading="lazy">
                        @if ($isVid)
                            <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                        @endif
                    </a>
                @endforeach

                @if ($count >= 5)
                    @php
                        $img3 = $images[3];
                        $isVid3 = ($img3['type'] ?? 'image') === 'video';
                        $dataType3 = $isVid3 ? (($img3['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                    @endphp
                    <a href="{{ $img3['path'] }}" class="glightbox gallery-item gallery-item-more" {!! $dataType3 !!}
                        data-gallery="gallery-{{ $gallery->id }}-strip">
                        <img src="{{ $img3['thumbnail'] ?? $img3['path'] }}" alt="{{ $img3['filename'] }}" loading="lazy">
                        @if ($isVid3)
                            <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                        @endif
                        <div class="gallery-overlay">
                            <span>+ {{ $count - 3 }}</span>
                        </div>
                    </a>
                @elseif ($count === 4)
                    @php
                        $img3 = $images[3];
                        $isVid3 = ($img3['type'] ?? 'image') === 'video';
                        $dataType3 = $isVid3 ? (($img3['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                    @endphp
                    <a href="{{ $img3['path'] }}" class="glightbox gallery-item" {!! $dataType3 !!}
                        data-gallery="gallery-{{ $gallery->id }}-strip">
                        <img src="{{ $img3['thumbnail'] ?? $img3['path'] }}" alt="{{ $img3['filename'] }}" loading="lazy">
                        @if ($isVid3)
                            <div class="gallery-video-overlay"><i class="bx bx-play"></i></div>
                        @endif
                    </a>
                @endif

                {{-- Médias cachés pour la bande --}}
                <div class="d-none">
                    @foreach ($images->slice(4) as $image)
                        @php
                            $isVid = ($image['type'] ?? 'image') === 'video';
                            $dataType = $isVid ? (($image['provider'] ?? '') === 'drive' ? 'data-type="iframe"' : 'data-type="video"') : '';
                        @endphp
                        <a href="{{ $image['path'] }}" class="glightbox" {!! $dataType !!}
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
