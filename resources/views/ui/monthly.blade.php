@extends("layouts.ui")

@section("objwater")
{
	type: 'bar',
	data: {
		labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		datasets: [
			{
				data: [100, 150, 200, 250, 300, 350, 400, 450, 500, 550, 600, 650],
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
				data: [120, 170, 220, 270, 320, 370, 420, 470, 520, 570, 620, 670],
				backgroundColor: 'yellow'
			}
		]
	}
}
@endsection
