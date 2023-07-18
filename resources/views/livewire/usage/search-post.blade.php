<div>
    <section id="breadcrumbs" class="breadcrumbs my-4 mt-5">
    </section>

    <section id="result" class="services section-bg">
        <div class="section-title">
            <h2 class="title-icon"><i class="bx bx-search-alt me-1 mt-1"></i>Résultat de Recherche</h2>
        @if ($searchedStr != "")
            @if ($foundPosts->total() == 0)
                <p> 0 résultat pour la recherche "{{$searchedStr}}"</p>
            @else
                <p> "{{$searchedStr}}" a été trouvé dans {{$foundPosts->total()}} article(s)</p>
            @endif
        </div>
            <div class="container col-6 d-flex flex-column">
                <div class="container mb-2">
            {{-- @foreach($foundPosts as $i => $post) --}}
            @foreach($foundPosts as $post)
                {{-- <div class="card my-1 flex-wrap search-card p-3" data-aos="zoom-in" data-aos-delay="{{ ($i  % $perPage + 1) * 100 }}"> --}}
                <div class="card my-1 flex-wrap search-card p-3" data-aos="zoom-in">
                    <div class="container">
                        <h4>
                            <a href="{{ $post->rubric->route().'/'.$post->id }}">
                                <!-- Icone -->
                                <div class="flex-row justify-content-between">
                                    <div class="icon"><i class="material-icons">{{ $post->icon }}</i></div>
                                    @if(!$post->published)
                                    <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger" title="non publié">unpublished</i>
                                    @endif
                                    @if($post->expired())
                                    <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger" title="expiré">auto_delete</i>
                                    @endif
                                    @if($post->forthcoming())
                                    <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger" title="à venir">schedule_send</i>
                                    @endif
                                </div>
                                <div>{{ $post->title }}</div>
                            </a>
                        </h4>

                        <div class="d-inline-flex">{!! preg_replace($replaceStr, " &thinsp; <strong>$searchedStr</strong> &thinsp;", $post->preview()) !!}</div>
                        {{-- <div class="d-inline-flex">{!! $searchPostManager->highlightResearch($searchedStr) !!}</div> --}}


                        <div class="position-absolute bottom-0 end-0 mt-2 me-2 serach-infos">
                            <p class="m-0"><i>Rubric : {{ $post->rubric->name }}</i></p>
                            <p class="mb-3 me-3"><i>Mis à jour depuis {{ $post->updated_at->format('d/m/Y') }}</i></p>
                        </div>
                    </div>
                </div>
            @endforeach

                </div>
                @include('includes.pagination', ['elements' => $foundPosts])
            </div>
        @endif
    </section>
</div>

