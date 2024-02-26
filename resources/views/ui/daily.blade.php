@extends("layouts.ui")
@section("title","Consumo diario.")
@section("subtitle","Gráficas que reflejan cuánto ha sido consumido durante este día con respecto a los 7 días anteriores.")
@section("objwater")
{
	type: 'line',
	data: {
		labels: ['Hace 7 días','Hace 6 días','Hace 5 días','Hace 4 días','Hace 3 días','Hace 2 días','Hace 1 día'],
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
		labels: ['Hace 7 días','Hace 6 días','Hace 5 días','Hace 4 días','Hace 3 días','Hace 2 días','Hace 1 día'],
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