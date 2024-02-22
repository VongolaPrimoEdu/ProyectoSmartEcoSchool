@extends("layouts.ui")
@section("objwater")
{
	type: 'line',
	data: {
		labels: ['L','M','X','J','V','S','D'],
		datasets: [
			{
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