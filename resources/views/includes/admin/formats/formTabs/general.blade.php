<div class="col">
  @error('format.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="form-group row">
        <label class="col-5 text-right my-auto" for="format-name">Nom</label>
        <input id="format-name" wire:model="format.name" type="input"
            class="col-6 form-control" placeholder="Nom de la mise en forme">
    </div>
  @error('format.bg_color')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="form-group row">
        <label class="col-5 text-right my-auto" for="format-bg-color">Couleur de fond</label>
        <select id="format-bg-color" wire:model="format.bg_color" type="input" class="col-6 form-control">
            <option label="Choisir la couleur de fond..."></option>
          @foreach($colors as $color)
            <option value='{{ $color }}' class="alert alert-{{ $color }}">{{ $color }}</option>
          @endforeach
        </select>
    </div>
  @error('format.border_style')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="form-group row">
        <label class="col-5 text-right my-auto" for="format-border-style">Style de bordure</label>
        <select id="format-border-style" wire:model="format.border_style" type="input" class="col-6 form-control">
            <option label="Choisir le style de bordure..."></option>
          @foreach($borders as $bKey => $border)
            <option value='{{ $border }}' style="{{ $border }}">{{ $bKey }}</option>
          @endforeach
        </select>
    </div>
  @error('format.title_color')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="form-group row">
        <label class="col-5 text-right my-auto" for="format-title-color">Couleur du titre</label>
        <select id="format-title-color" wire:model="format.title_color" type="input" class="col-6 form-control">
            <option label="Choisir la couleur du titre..."></option>
          @foreach($colors as $color)
            <option value='text-{{ $color }}' class="alert alert-{{ $color }}">{{ $color }}</option>
          @endforeach
        </select>
    </div>
  @error('format.subtitle_color')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="form-group row">
        <label class="col-5 text-right my-auto" for="format-subtitle-color">Couleur du sous-titre</label>
        <select id="format-title-color" wire:model="format.subtitle_color" type="input" class="col-6 form-control">
            <option label="Choisir la couleur du sous-titre..."></option>
          @foreach($colors as $color)
            <option value='text-{{ $color }}' class="alert alert-{{ $color }}">{{ $color }}</option>
          @endforeach
        </select>
    </div>
    <div class="row justify-content-center mb-3">
        <div class="col-3">
            <dt class="text-center mb-1">Aperçu</dt>
            <div class="google-visualization-orgchart-node p-1" style="{{ $format->style }}">
                <div class='font-weight-bold {{ $format->title_color }}'>Titre</div>
                <div class='{{ "{$format->subtitle_font_style} {$format->subtitle_color}" }}'>sous-titre</div>
            </div>
        </div>
    </div>
</div>
