@extends("layouts.ui")
@section("title","Consumo del día actual.")
@section("subtitle","Gráficas que muestran cuánto se está consumiendo durante este día.")
@section("objwater")
{
	type: 'line',
	data: {
		labels: ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00',
		'08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00',
		'17:00','18:00','19:00','20:00','21:00','22:00','23:00'
		],
		datasets: [
			{
				data: [
					@foreach ($consumo_por_hora_agua as $consumo_hora_agua)
						{{$consumo_hora_agua}},
					@endforeach
				],
				borderColor: 'rgb(0,0,255)',
				backgroundColor: 'rgba(0,0,255,0.2)'
			}
		]
	},
	options: {
		elements: {
			line: {
				fill: true,
				tension: 0.5
			}
		}
	}
}
@endsection
@section("objelec")
{
	type: 'line',
	data: {
		labels: ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00',
		'08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00',
		'17:00','18:00','19:00','20:00','21:00','22:00','23:00'
		],
		datasets: [
			{
				data: [
					@foreach ($consumo_por_hora_electricidad as $consumo_hora_elec)
						{{$consumo_hora_elec}},
					@endforeach
				],
				borderColor: 'rgb(143,143,0)',
				backgroundColor: 'rgba(143,143,0,0.2)'
			}
		]
	},
	options: {
		elements: {
			line: {
				fill: true,
				tension: 0.5
			}
		}
	}
}
@endsection
@section("viewforimage","currentday")