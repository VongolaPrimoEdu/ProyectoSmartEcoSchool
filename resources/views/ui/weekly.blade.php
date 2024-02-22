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
				borderColor: 'blue'
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
				borderColor: 'yellow'
			}
		]
	}
}
@endsection