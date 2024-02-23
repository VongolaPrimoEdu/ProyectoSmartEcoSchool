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
				<div class="swiper-slide" id="daily">
					<p class="consume-type">DIARIO</p>
				</div>
				<div class="swiper-slide" id="weekly">
					<p class="consume-type">SEMANAL</p>
				</div>
				<div class="swiper-slide" id="monthly">
					<p class="consume-type">MENSUAL</p>
				</div>
				<div class="swiper-slide" id="percentages">
					<p class="consume-type">PORCENTAJES</p>
				</div>
			</div>
		</div>
		<h1>@yield("title")</h1>
	</header>
	<main>
			<img src="{{asset("img/icons/water.svg")}}" class="icon" alt="Gota de agua">
			<img src="{{asset("img/icons/lightning.svg")}}" class="icon" alt="Rayo de electricidad">
			<div><canvas id="consumo-agua"></canvas></div>
			<div><canvas id="consumo-electricidad"></canvas></div>
	</main>
	<script type="module">
		import  { drawElecChart,drawWaterChart }  from "./js/charts.js";
		drawWaterChart(@yield("objwater"));
		drawElecChart(@yield("objelec"));
		setTimeout(() => location.href = "@yield("next-location")", @yield("time"));
		document.getElementById("@yield("viewforimage")").insertAdjacentHTML("beforeend",
		"<img src='{{asset('img/icons/arrow.svg')}}' id='arrow' alt='Flecha apuntando hacia arriba'>");
	</script>
	<h2>@yield("subtitle")</h2>
</body>
</html>