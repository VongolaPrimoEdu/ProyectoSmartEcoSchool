@extends("layouts.ui")
@section("title","Consumo semanal.")
 @section("subtitle","Gráficas que reflejan cuánto está consumiéndose a lo largo de la semana en comparación con el consumo de semanas anteriores.")
@section("objwater")
{
	type: 'line',
	data: {
		labels: ['5 semanas','4 semanas','3 semanas','2 semanas','1 semana'],
		datasets: [
			{
				label: '',
				data: [
					@foreach ($consumo_por_semana as $consumo_semana)
						{{$consumo_semana["agua"]}},
					@endforeach
				],
				borderColor: 'rgb(0,0,255)',
				backgroundColor: 'rgba(0,0,255,0.5)'
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
		labels: ['5 semanas','4 semanas','3 semanas','2 semanas','1 semana'],
		datasets: [
			{
				label: '',
				data: [
					@foreach ($consumo_por_semana as $consumo_semana)
						{{$consumo_semana["electricidad"]}},
					@endforeach
				],
				borderColor: 'rgb(143,143,0)',
				backgroundColor: 'rgba(143,143,0,0.5)'
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
@section("next-location", route("ui.monthly"))
@section("time",15000)
@section("viewforimage","weekly")