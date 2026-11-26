@extends('errors.layout')

@section('code', '404')
@section('title', 'Hier mist een stukje')
@section('message', 'Deze pagina bestaat niet (meer).')

@section('tips')
    <li>Check of de link goed is overgetypt.</li>
    <li>Kwam je via een oude link of QR-code? Die kan verlopen zijn.</li>
@endsection
