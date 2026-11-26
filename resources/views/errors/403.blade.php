@extends('errors.layout')

@section('code', '403')
@section('title', 'Dit stukje is niet voor jou')
@section('message', 'Je hebt geen rechten om deze pagina te bekijken.')

@section('tips')
    <li>Ben je student? Je workshops vind je op je eigen dashboard.</li>
    <li>Hoor je wel toegang te hebben? Log uit en log in met het goede account.</li>
@endsection
