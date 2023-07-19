<div>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

    <section id="infos" class="services section-bg">
        <div class="section-title">
            <h2 class="title-icon"><i class="material-icons fs-1 me-2">{{ $rubric->icon }}</i>{{ $rubric->title }}</h2>
            <p>{{ $rubric->description }}</p>
        </div>
        <div class="container row col-8 ms-auto me-auto">
            <div class="card rounded-pill d-flex flex-row"> {{-- ou rounded-4 --}}
                <div class="col-md-2 m-5">
                    @if ($user->avatar)
                        <img src="{{asset('img')}}/{{$user->avatar}}" width="100%" />
                    @else
                        {{-- <img src="{{asset('img/profil-pic_dummy.png')}}" width="100%" /> --}}
                        <img src="{{asset('img/fou.png')}}" width="100%" />
                    @endif
                </div>
                <div class="col-md-4 m-5 pt-5">
                    <p>{{ $user->gender == 'F' ? 'Mme.' : 'M.' }} {{ $user->name }}</p>
                    <p>Prénom : {{ $user->first_name }}</p>
                    <p>E-mail : {{ $user->email }}</p>

                    @foreach($user->profiles as $profile)
                        <option value="{{ $profile->id }}">
                            {{ $profile->first_name }}
                        </option>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
