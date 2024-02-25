@extends("layouts.ui")
@section("title","Consumo diario.")
@section("subtitle","Gráficas que reflejan cuánta agua y electricidad han sido consumidas durante este día con respecto a los 7 días anteriores.")
@section("objwater")
{
	type: 'line',
	data: {
		labels: ['L','M','X','J','V','S','D'],
		datasets: [
			{
				label: "",
				data: [
					@foreach ($consumo_por_dia as $consumo_dia)
						{{$consumo_dia["agua"]}},
					@endforeach
				],
				borderColor: 'rgb(0,0,255)',
				backgroundColor: 'rgba(0,0,255,0.5)'
			}
		]
	}
}
@endsection
@section("objelec")
{
	type: 'line',
	data: {
		labels: ['L','M','X','J','V','S','D'],
		datasets: [
			{
				label: "",
				data: [
					@foreach ($consumo_por_dia as $consumo_dia)
						{{$consumo_dia["electricidad"]}},
					@endforeach
				],
				borderColor: 'rgb(143,143,0)',
				backgroundColor: 'rgba(143,143,0,0.5)'
			}
		]
	}
}
@endsection
@section("next-location", route("ui.weekly"))
@section("time",20000)
@section("viewforimage","daily")