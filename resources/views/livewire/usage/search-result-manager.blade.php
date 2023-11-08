<div>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

    <section id="result" class="services section-bg">
        <div class="section-title">
            <h2 class="title-icon"><i class="material-icons fs-1 me-2">{{ $rubric->icon }}</i>{{$rubric->title}}</h2>
          @if ($searchedStr != "")
           @if ($foundPosts->total() == 0)
            <p>0 résultat pour la recherche "{{$searchedStr}}"</p>
           @else
            <p>"{{$searchedStr}}" a été trouvé dans {{$foundPosts->total()}} article(s)</p>
           @endif
          @endif
        </div>
      @if ($searchedStr != "")
        <div class="container col-6 d-flex flex-column" @if ($firstLoad) data-aos="fade-up" @endif>
            <div class="container mb-2">
          @foreach($foundPosts as $i => $post)
            <div class="flex-wrap" data-aos="zoom-in" @if ($firstLoad) data-aos-delay="{{ ($i  % $perPage + 1) * 100 }}" @endif>
                <div class="container search-card card my-1 p-3">
                    <h4>
                        <a href="{{ route('post.index', ['rubric' => $post->rubric->route(), 'post_id' => $post->id]) }}">
                            <!-- Icone -->
                            <div class="flex-row justify-content-between">
                                <div class="icon"><i class="material-icons">{{ $post->icon }}</i></div>
                              @if (!$post->released && is_object($post->status))
                                <i class="position-absolute top-0 end-0 mt-2 me-2 material-icons text-danger"
                                   title="{{ $post->status->title }}">{{ $post->status->icon }}</i>
                              @endif
                        </div>
                            <div>{{ $post->title }}</div>
                        </a>
                    </h4>
                    <div class="d-inline-flex">
                        {!! preg_replace($replaceStr, " &thinsp; <strong>$searchedStr</strong> &thinsp;", $post->preview()) !!}
                    </div>
                    <div class="position-absolute bottom-0 end-0 mt-2 me-2 serach-infos">
                        <p class="m-0"><i>Rubrique : {{ $post->rubric->name }}</i></p>
                        <p class="mb-3 me-3"><i>Mis à jour le {{ $post->updated_at->format('d/m/Y') }}</i></p>
                    </div>
                </div>
            </div>
          @endforeach

            </div>
            @include('includes.pagination', ['elements' => $foundPosts])
        </div>
      @else
        <p class="text-center">{{$rubric->description}}</p>
      @endif
    </section>
</div>
