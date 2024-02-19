<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class UIController extends Controller
{
	public function daily()
	{
		// Obtener la fecha actual
		$fecha = Carbon::now()->toDateString();
	
		// Obtener las medidas de consumo de electricidad y agua para el día actual
		$consumo_por_hora_electricidad = $this->getConsumoPorHora(1, $fecha); // Tipo de sensor para electricidad: 1
		$consumo_por_hora_agua = $this->getConsumoPorHora(2, $fecha); // Tipo de sensor para agua: 2
	
		return view('daily', compact('consumo_por_hora_electricidad', 'consumo_por_hora_agua'));
	}
	
	private function getConsumoPorHora($id_tipo_sensor, $fecha)
	{
		$consumo_por_hora = Measurement::whereHas('sensor', function ($query) use ($id_tipo_sensor) {
			$query->where('id_type', '=', $id_tipo_sensor);
		})->whereDate('fecha', $fecha)
		  ->select(DB::raw('HOUR(fecha) as hora'), DB::raw('SUM(consumo) as consumo'))
		  ->groupBy(DB::raw('HOUR(fecha)'))
		  ->pluck('consumo', 'hora');
	
		return $consumo_por_hora;
	}
	
    public function weekly()
		{

		}
    public function monthly()
		{

		}
    public function percentages()
		{

		}
}




