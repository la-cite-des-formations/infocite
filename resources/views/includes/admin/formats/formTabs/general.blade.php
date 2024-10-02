<div class="col pt-2">
  @error('format.name')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-5 col-form-label text-end" for="format-name">Nom</label>
        <div class="col-6">
            <input  id="format-name" wire:model="format.name" type="input"
                    class="form-control" placeholder="Nom de la mise en forme">
        </div>
    </div>
  @error('format.bg_color')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-5 col-form-label text-end" for="format-bg-color">Couleur de fond</label>
        <div class="col-6">
            <select id="format-bg-color" wire:model="format.bg_color" type="input" class="form-select">
                <option label="Choisir la couleur de fond..."></option>
              @foreach($colors as $color)
                <option value='{{ $color }}' class="alert alert-{{ $color }}">{{ $color }}</option>
              @endforeach
            </select>
        </div>
    </div>
  @error('format.border_style')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-5 col-form-label text-end" for="format-border-style">Style de bordure</label>
        <div class="col-6">
            <select id="format-border-style" wire:model="format.border_style" type="input" class="form-select">
                <option label="Choisir le style de bordure..."></option>
              @foreach($borders as $bKey => $border)
                <option value='{{ $border }}' style="{{ $border }}">{{ $bKey }}</option>
              @endforeach
            </select>
        </div>
    </div>
  @error('format.title_color')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-5 col-form-label text-end" for="format-title-color">Couleur du titre</label>
        <div class="col-6">
            <select id="format-title-color" wire:model="format.title_color" type="input" class="form-select">
                <option label="Choisir la couleur du titre..."></option>
              @foreach($colors as $color)
                <option value='text-{{ $color }}' class="alert alert-{{ $color }}">{{ $color }}</option>
              @endforeach
            </select>
        </div>
    </div>
  @error('format.subtitle_color')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-5'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-5 col-form-label text-end" for="format-subtitle-color">Couleur du sous-titre</label>
        <div class="col-6">
            <select id="format-title-color" wire:model="format.subtitle_color" type="input" class="form-select">
                <option label="Choisir la couleur du sous-titre..."></option>
              @foreach($colors as $color)
                <option value='text-{{ $color }}' class="alert alert-{{ $color }}">{{ $color }}</option>
              @endforeach
            </select>
        </div>
    </div>
    <div class="row justify-content-center mb-3">
        <div class="col-3">
            <dt class="text-center">Aperçu</dt>
            <div class="google-visualization-orgchart-node p-1" style="{{ $format->style }}">
                <div class='fw-bold {{ $format->title_color }}'>Titre</div>
                <div class='{{ "{$format->subtitle_font_style} {$format->subtitle_color}" }}'>sous-titre</div>
            </div>
        </div>
    </div>
</div>
