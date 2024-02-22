<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Plantilla de UI de Smartecoschool</title>
	<link rel="stylesheet" href="{{asset("css/app.css")}}">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
	<header>
		<img id="logo" src="{{asset("img/logo.png")}}" alt="Logotipo de Smartecoschool">
		<div class="swiper-container">
			<p>CONSUMO</p>
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<p class="consume-type">DIARIO</p>
					{{-- <img src="{{asset("img/icons/arrow.svg")}}" alt="" style="width:20%;"> --}}
				</div>
				<div class="swiper-slide">
					<p class="consume-type">SEMANAL</p>
				</div>
				<div class="swiper-slide">
					<p class="consume-type">MENSUAL</p>
				</div>
				<div class="swiper-slide">
					<p class="consume-type">PORCENTAJES</p>
				</div>
			</div>
		</div>
		<h1>@yield("title")</h1>
	</header>
	<main>
			<img src="{{asset("img/icons/water.svg")}}" alt="Gota de agua">
			<img src="{{asset("img/icons/lightning.svg")}}" alt="Rayo de electricidad">
			<div><canvas id="consumo-agua"></canvas></div>
			<div><canvas id="consumo-electricidad"></canvas></div>
	</main>
	<script type="module">
		import  { drawElecChart,drawWaterChart }  from "./js/charts.js";
		drawWaterChart(@yield("objwater"));
		drawElecChart(@yield("objelec"));
	</script>
	<h2>@yield("subtitle")</h2>
</body>
</html>