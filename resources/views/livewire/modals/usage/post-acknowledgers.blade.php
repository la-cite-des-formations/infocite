@extends('layouts.modal')

@section('modal-title', 'Utilisateurs ayant acquitté l\'article')

@section('modal-body')

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Date d'acquittement</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($acknowledgers as $user)
                    <tr>
                        <td>
                            <i class="bx {{ $user->is_staff ? 'bx-briefcase text-primary' : 'bx-user text-secondary' }} me-2"></i>
                            {{ $user->identity }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($user->pivot->occurred_at)->format('d/m/Y à H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">
                            Aucun acquittement pour le moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
@endsection
