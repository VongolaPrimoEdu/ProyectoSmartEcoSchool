@extends("layouts.ui")
@section("title","Porcentaje de consumo mensual.")
@section("subtitle","Gráficas que muestran la cantidad de agua y electricidad consumidas por cada mes del año.")
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
							{{$consumo_mes["electricidad"]}},
					@endforeach	
				],
				backgroundColor: 'rgb(143,143,0)'
			}
		]
	}
}
@endsection
@section("next-location", route("ui.daily"))
@section("time",20000)
@section("viewforimage","monthly")