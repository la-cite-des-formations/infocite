@extends('star.app')

@section('tabSubtitle', " : Star (Médiation->Bilan)")

@section ('content')
    <div class="content">
        <div class="flex">
            <a href="/star/educatif.mediation" class="btn">Retour</a>
        </div>
    </div>
@endsection



<style>
    .content {
        margin: 15px;
    }
    
    a.btn{
        color: #858585;
        background-color: #d7d7d7;
        font-weight: 600;
    }
</style>