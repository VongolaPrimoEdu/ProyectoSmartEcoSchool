@extends("layouts.ui")

@section("objwater")
{
	type: 'bar',
	data: {
		labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		datasets: [
			{
				data: [
					@foreach ($consumo_por_mes as $consumo_mes)
							{{$consumo_mes["agua"]}}
					@endforeach	
				],
				backgroundColor: 'blue',
        label: ""
			}
		]
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
				data: [
					@foreach ($consumo_por_mes as $consumo_mes)
							{{$consumo_mes["electricidad"]}}
					@endforeach	
				],
				backgroundColor: 'yellow'
			}
		]
	}
}
@endsection
