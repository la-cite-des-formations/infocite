<section wire:key='searchResult' id="result" class="services section-bg">
    <div class="section-title">
        <h2 class="title-icon"><i class="material-icons fs-1 me-2">{{ $rubric->icon }}</i>{{$rubric->title}}</h2>
      @if($foundPosts->isNotEmpty())
        <p>{{ "\"{$searchedStr}\" a été trouvé dans {$foundPosts->total()} article".($foundPosts->total() > 1 ? 's' : '') }}</p>
      @else
        <p>Aucun article trouvé correspondant à votre recherche...</p>
      @endif
    </div>
    <div class="container d-flex flex-column">
      @if($foundPosts->isNotEmpty())
        <div class="container mb-2">
          @foreach($foundPosts as $i => $post)
            <div wire:key='{{$post->id}}' wire:click='redirectToPost({{ $post->id }})' role="button" class="flex-wrap"
                 data-aos="zoom-in" data-aos-delay="{{ ($i  % $postsPerPage + 1) * 100 }}">
                <div class="container icon-box post-result d-flex my-1">
                    <div class="col-8">
                        <h4 class="d-flex mb-0">
                            <div class="icon mb-0 me-1">
                                <i class="material-icons">{{ $post->icon }}</i>
                            </div>
                            <a>{{ $post->title }}</a>
                        </h4>
                        <p class="d-inline-flex">
                            {!! preg_replace($replaceStr, "<strong>{$searchedStr}</strong>", $post->preview()) !!}
                        </p>
                    </div>
                    <div class="col-4 d-flex flex-column justify-content-end text-end me-2 search-infos">
                        <i>Rubrique : {{ $post->rubric->name }}</i>
                        <i>Mis à jour le {{ $post->updated_at->format('d/m/Y') }}</i>
                    </div>
                </div>
            </div>
          @endforeach
        </div>
        @include('includes.pagination', ['elements' => $foundPosts, 'perPage' => 'postsPerPage'])
      @endif
    </div>
    <div class="section-title">
        <h2>&nbsp</h2>
      @if($foundApps->isNotEmpty())
        <p>{{ "\"{$searchedStr}\" a été trouvé dans {$foundApps->total()} application".($foundApps->total() > 1 ? 's' : '') }}</p>
      @else
        <p>Aucune application trouvée correspondant à votre recherche...</p>
      @endif
    </div>
  @if($foundApps->isNotEmpty())
    <div class="container pricing" @if ($firstLoad) data-aos="fade-up" @endif>
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th class="ps-3">Application</th>
                    <th>Descritpion</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($foundApps as $i => $app)
                <tr @can('view', $app)
                        wire:click="redirectToApp('{{ $app->url }}')" role="button"
                    @else
                        class="table-secondary opacity-50"
                        title="Si besoin, veuillez demander l'accès à votre responsable"
                    @endif>
                    <td class="p-0">
                        <h3 class="d-flex p-3 m-0">
                          @if (empty($app->favicon))
                            <span class="material-icons ms-0 me-2">{{ $app->icon }}</span>
                          @else
                            <img src="{{ $app->favicon }}" class="me-2" style="width: 25px; height: 25px;">
                          @endif
                            {{ $app->name }}
                        </h3>
                    </td>
                    <td>
                        <h4 class="m-0">
                            <span>{{ $app->description }}</span>
                        </h4>
                    </td>
                    <td>
                      @can('handle', $app)
                        <div wire:click.prefetch='blockRedirection' class="input-group" role="group" aria-label="Actions">
                            <a href="{{ route('personal-apps.edit', ['app_id' => $app->id]) }}"
                                role="button" class="btn btn-sm btn-success" title="Modifier">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <button wire:click="showModal('confirm', {handling : 'deleteApp', appId : {{ $app->id }}})"
                                    type="button" class="btn btn-sm btn-danger" title="Supprimer">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                      @endcan
                    </td>
                </tr>
              @endforeach
            </tbody>
        </table>
    </div>
    <div class="container">
        @include('includes.pagination', ['elements' => $foundApps, 'perPage' => 'appsPerPage'])
    </div>
  @endif
</section>
