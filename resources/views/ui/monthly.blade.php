@extends("layouts.ui")
@section("title","Porcentaje de consumo mensual.")
@section("subtitle","Gráficas que muestran la cantidad consumida este mes con respecto de cada cantidad de cada mes de este año.")
@section("objwater")
{
	type: 'bar',
	data: {
		labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		datasets: [
			{
				data: [
					@foreach ($consumo_por_mes as $consumo_mes)
							{{$consumo_mes["agua"]}},
					@endforeach	
				],
				backgroundColor: 'blue',
        label: ""
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
	type: 'bar',
	data: {
		labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		datasets: [
			{
				label: "",
				data: [
					@foreach ($consumo_por_mes as $consumo_mes)
							{{$consumo_mes["electricidad"]}},
					@endforeach	
				],
				backgroundColor: 'rgb(143,143,0)'
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
@section("next-location", route("ui.currentday"))
@section("time",20000)
@section("viewforimage","monthly")