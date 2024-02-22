@extends("layouts.ui")
@section("objwater")
{
	type: 'line',
	data: {
		labels: ['L','M','X','J','V','S','D'],
		datasets [
			{
				data: [100,150,200,250,300,350,400],
				borderColor: blue
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
		datasets [
			{
				data: [100,150,200,250,300,350,400],
				borderColor: yellow
			}
		]
	}
}
@endsection