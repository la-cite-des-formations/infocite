<div class="col pt-2">
  @error('post.title')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end" for="post-title">Titre</label>
        <div class="col-7">
            <input id="post-title" wire:model="post.title" type="input"
               class="form-control" placeholder="Titre de l'article">
        </div>
    </div>
  @error('post.icon')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end" for="post-icon">Icône</label>
        @include('includes.icon-picker', ['model' => 'post', 'inAdminInterface' => TRUE])
    </div>
    <div>
      @error('post.content')
        @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
      @enderror
    </div>
    <div class="row g-2 align-items-center mb-2">
        <label class="col-1 col-form-label text-end mb-auto" for="post-content">Contenu</label>
        <div wire:ignore class="col-10">
            <textarea id="post-content" wire:model="post.content" type="input"
                      class="form-control tinymce" placeholder="Contenu de l'article">
            </textarea>
        </div>
    </div>
  @error('post.rubric_id')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
        </div>
    </div>
    <div class="row g-2 align-items-center mb-3">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="alert alert-warning alert-dismissible fade show mb-2" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                <strong>Note :</strong> Les modifications de la galerie (envoi, suppression, ordre) ne seront définitivement enregistrées que lorsque vous <strong>sauvegarderez l'article</strong> (bouton en bas de page).
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <livewire:usage.post-gallery :postId="$post->id" :wire:key="'gallery-admin-' . ($post->id ?? 'new')" />
        </div>
    </div>
  @error('post.published')
    @include('includes.rules-error-message', ['labelsColLg' => 'col-1'])
  @enderror
    <div class="row g-2 align-items-center mb-2">
        <div class="col-1 form-check-label text-end" for="post-published">Publier</div>
        <div class="form-check col ms-1">
            <input  id="post-published" wire:model="post.published"
                    type="checkbox" class="form-check-input">
        </div>
    </div>
    <div class="row g-2 align-items-center mb-2">
        <div class="col-1 form-check-label text-end" for="post-acknowledgment">Accusé de réception</div>
        <div class="form-check col ms-1">
            <input  id="post-acknowledgment" wire:model="post.is_acknowledgment_required"
                    type="checkbox" class="form-check-input">
        </div>
    </div>
    <div class="row g-2 align-items-center mb-2">
        <div class="col-1 form-check-label text-end" for="post-rating">Système de notation</div>
        <div class="form-check col ms-1">
            <input  id="post-rating" wire:model="post.is_rating_enabled"
                    type="checkbox" class="form-check-input">
        </div>
    </div>
</div>
