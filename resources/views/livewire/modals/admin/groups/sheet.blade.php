@extends('layouts.modal')

@section('modal-title', "Visualisation")

@section('modal-header-options')
    <a wire:click="switchMode('edition')" role="button"
       title="Modifier" class="text-secondary mr-1" id="switchModeEditionButton">
        <span class="material-icons">mode</span>
    </a>
@endsection

@section('modal-body')
    <div class="alert alert-success mb-3">
        <div class="d-flex">
            <div class="my-auto mr-3">
                <span class="material-icons-outlined md-36">groups</span>
            </div>
            <div class="my-auto">
                <h5>{{ $group->name }}</h5>
            </div>
            <div class="ml-auto my-auto">{{ AP::getGroupType($group->type) }}</div>
        </div>
    </div>
    <div class="alert alert-info mb-0">
        <div class="font-weight-bold">Membres du groupe</div>
        <ul>
          @foreach($group->users as $user)
            <li @if($user->is_frozen) class="alert-warning" @endif>
                {{ "$user->first_name $user->name" }}
              @if($group->type === 'S')
                {{ $user->rolesList($group->id, " - %%") }}
              @else
                {{ $user->function($group->id, " - %%") }}
              @endif
            </li>
          @endforeach
        </ul>
        <dl class="row mb-0 mx-0">
            <dt class="col-3 text-right pl-0">Code YParéo</dt>
            <dd class="col-9 pl-0">{{ $group->code_ypareo }}</dd>
        </dl>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
@endsection
