@extends("layouts.ui")
@section("title","Consumo diario.")
@section("subtitle","Gráficas que reflejan cuánto ha sido consumido durante este día con respecto a los 7 días anteriores.")
@section("objwater")
{
	type: 'line',
	data: {
		labels: ['7 días','6 días','5 días','4 días','3 días','2 días','1 día'],
		datasets: [
			{
				label: "",
				data: [
					@foreach ($consumo_por_dia as $consumo_dia)
						{{$consumo_dia["agua"]}},
					@endforeach
				],
				borderColor: 'rgb(0,0,255)'
			}
		]
	},
	options : {
		scales: {
			y: {
				ticks: {
						color: 'black',
						font: {
							weight: 'bold',
							size: 20
						}
				}
			},
			x: {
				ticks: {
						color: 'black',
						font: {
							weight: 'bold',
							size: 18
						}
				}
			}
		}
		}
	}
@endsection
@section("objelec")
{
	type: 'line',
	data: {
		labels: ['7 días','6 días','5 días','4 días','3 días','2 días','1 día'],
		datasets: [
			{
				label: "",
				data: [
					@foreach ($consumo_por_dia as $consumo_dia)
						{{$consumo_dia["electricidad"]}},
					@endforeach
				],
				borderColor: 'rgb(143,143,0)'
			}
		]
	},
	options : {
		scales: {
			y: {
				ticks: {
						color: 'black',
						font: {
							weight: 'bold',
							size: 20
						}
				}
			},
			x: {
				ticks: {
						color: 'black',
						font: {
							weight: 'bold',
							size: 18
						}
				}
			}
		}
	}
}
@endsection
@section("next-location", route("ui.weekly"))
@section("time",15000)
@section("viewforimage","daily")