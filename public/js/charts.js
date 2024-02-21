const WATER_CANVAS = document.getElementById("consumo-agua");
const ELECTRICITY_CANVAS = document.getElementById("consumo-electricidad");

function drawWaterChart(obj) {
	new Chart(WATER_CANVAS, obj);
}

function drawElecChart(obj) {
	new Chart(ELECTRICITY_CANVAS, obj);
}

export {drawWaterChart, drawElecChart};