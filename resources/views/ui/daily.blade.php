@extends("layouts.ui")
@section("title","Consumo diario.")
@section("subtitle","Gráficas que reflejan cuánta agua y electricidad han sido consumidas durante este día.")
@section("objwater")
@endsection
@section("objelec")
@endsection
@section("next-location", route("ui.weekly"))
@section("time",20000)
@section("viewforimage","daily")