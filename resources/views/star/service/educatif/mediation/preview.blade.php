@extends('star.app')

@section('tabSubtitle', " : Star (Médiation)")

@section ('content')
<div class="content">
    <div class="flex btn-nav">
        <a href="" class="create_entretien btn">Créer un entretien</a>
        <a href="/star/educatif.mediation/bilan" class="go_to_bilan btn">Accéder au Bilan</a>
    </div>
    <br>
    <div class="flex l2">
        <form action="" class="flex">
            <input type="text" name="studient" id="studient" placeholder="Apprenant ...">
            <select name="sector" id="sector">
                <option value="" disabled selected>Secteur</option>
            </select>
            <select name="training" id="training">
                <option value="" disabled selected>Formation</option>
            </select>
            <select name="level" id="level">
                <option value="" disabled selected>Niveau</option>
            </select>
            <select name="motif" id="motif">
                <option value="" disabled selected>Motif</option>
            </select>
            <select name="visit" id="visit">
                <option value="" disabled selected>Visite</option>
            </select>
        </form>
        <div class="">
            <p>Entretiens : 00</p>
            <p>Personnes vues : 00</p>
        </div>
    </div>

    <table class="table table-hover">
        <tr>
            <th>Date</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Secteur</th>
            <th>Formation</th>
            <th>Niveau</th>
            <th>Motif</th>
            <th>Origine du signalement</th>
            <th>Visite</th>
        </tr>
        <tr>
            <td>rgdd</td>
            <td>reg</td>
            <td>gfdg</td>
            <td>fgdf</td>
            <td>drgd</td>
            <td>rgdrgddd</td>
            <td>drgdrgd</td>
            <td>grdgdr</td>
            <td>rgdrgdr</td>
            <td><a href="" class="btn view_entretien">Voir l'entretien</a></td>
        </tr>
        <tr>
            <td>rgdd</td>
            <td>reg</td>
            <td>gfdg</td>
            <td>fgdf</td>
            <td>drgd</td>
            <td>rgdrgddd</td>
            <td>drgdrgd</td>
            <td>grdgdr</td>
            <td>rgdrgdr</td>
            <td><a href="" class="btn view_entretien">Voir l'entretien</a></td>
        </tr>
        <tr>
            <td>rgdd</td>
            <td>reg</td>
            <td>gfdg</td>
            <td>fgdf</td>
            <td>drgd</td>
            <td>rgdrgddd</td>
            <td>drgdrgd</td>
            <td>grdgdr</td>
            <td>rgdrgdr</td>
            <td><a href="" class="btn view_entretien">Voir l'entretien</a></td>
        </tr>
    </table>
</div>

@endsection



<style>
    .content {
        margin: 15px;
    }

    .btn-nav {
        justify-content: space-between;
        width: 30%;
    }

    .create_entretien.btn {
        color: white;
        background-color: #13AFD1;
    }

    .go_to_bilan.btn {
        color: white;
        background-color: #6FF595;
    }

    
    .l2{
        justify-content: space-between;
        width: 70%;
    }
    form {
        justify-content: space-between;
        width: 80%;
    }

    .view_entretien.btn {
        background-color: #F383C6;
    }

    table{
        width: 100%;
        text-align: center;
    }

    th{
        /* background-color: #13AFD1; */
        border-color: 2px solid red;
    }
</style>