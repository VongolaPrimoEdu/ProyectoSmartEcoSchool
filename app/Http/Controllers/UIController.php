<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Measurement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UIController extends Controller
{
	public function daily()
	{
		// Obtener la fecha actual
		$fecha = Carbon::now()->toDateString();
	
		// Obtener las medidas de consumo de electricidad y agua para el día actual
		$consumo_por_hora_electricidad = $this->getConsumoPorHora(1, $fecha); // Tipo de sensor para electricidad: 1
		$consumo_por_hora_agua = $this->getConsumoPorHora(2, $fecha); // Tipo de sensor para agua: 2
	
		return view('ui.daily', compact('consumo_por_hora_electricidad', 'consumo_por_hora_agua'));
	}
	
	private function getConsumoPorHora($id_tipo_sensor, $fecha)
	{
		$sensor_type_query = DB::select(DB::raw('SELECT id_sensor FROM sensors WHERE id_type=1'));
		$consumo_por_hora = DB::table('measurements')
		->select(DB::raw("HOUR(fecha) as hora, consumo"))
		->whereIn('id_sensor', collect(DB::select(DB::raw('SELECT id_sensor FROM sensors WHERE id_type=1')))->pluck('id_sensor')->toArray())
		->where('DATE(fecha)', '=', "$fecha")
		->get()->toArray();
		return $consumo_por_hora;
	}
	
    public function weekly()
{
    // Obtener la fecha actual
    $fecha_actual = Carbon::now();

    // Arreglo para almacenar los datos de consumo por día de la semana
    $consumo_por_dia = [];

    // Recorrer los últimos 7 días
    for ($i = 0; $i < 7; $i++) {
        // Obtener la fecha para el día actual menos $i días
        $fecha = $fecha_actual->subDays($i)->toDateString();

        // Obtener los datos de consumo por hora para esa fecha
        $consumo_por_hora_electricidad = $this->getConsumoPorHora(1, $fecha); // Tipo de sensor para electricidad: 1
        $consumo_por_hora_agua = $this->getConsumoPorHora(2, $fecha); // Tipo de sensor para agua: 2

        // Sumar el consumo total por hora para obtener el consumo diario
        $consumo_diario_electricidad = array_sum($consumo_por_hora_electricidad);
        $consumo_diario_agua = array_sum($consumo_por_hora_agua);

        // Almacenar los datos en el arreglo por día de la semana
        $dia_semana = $fecha_actual->dayName;
        $consumo_por_dia[$dia_semana] = [
            'electricidad' => $consumo_diario_electricidad,
            'agua' => $consumo_diario_agua,
        ];
    }

    return view('ui.weekly', compact('consumo_por_dia'));
}
    public function monthly()
		{
   // Obtener el año y mes actual
   $anio_actual = Carbon::now()->year;
   $mes_actual = Carbon::now()->month;

   // Obtener el consumo total de electricidad y agua para el mes actual
   $consumo_total_electricidad = $this->getConsumoTotal(1, $anio_actual, $mes_actual); // Tipo de sensor para electricidad: 1
   $consumo_total_agua = $this->getConsumoTotal(2, $anio_actual, $mes_actual); // Tipo de sensor para agua: 2

   return view('ui.monthly', compact('consumo_total_electricidad', 'consumo_total_agua'));
}

private function getConsumoTotal($id_tipo_sensor, $anio, $mes)
{
   $consumo_total = Measurement::whereHas('sensor', function ($query) use ($id_tipo_sensor) {
	   $query->where('id_type', '=', $id_tipo_sensor);
   })->whereYear('fecha', $anio)
	 ->whereMonth('fecha', $mes)
	 ->sum('consumo');

   return $consumo_total;
		}
    public function percentages()
		{

		  // Obtener la fecha actual
		  $fecha_actual = Carbon::now();

		  // Arreglo para almacenar los porcentajes de aumento o disminución por día
		  $porcentajes = [];
	  
		  // Recorrer los últimos 7 días
		  for ($i = 0; $i < 7; $i++) {
			  // Obtener la fecha para el día actual menos $i días
			  $fecha = $fecha_actual->subDays($i)->toDateString();
	  
			  // Obtener el consumo por hora para el día actual y el día anterior
			  $consumo_actual_electricidad = $this->getConsumoTotalPercentage(1, $fecha); // Tipo de sensor para electricidad: 1
			  $consumo_anterior_electricidad = $this->getConsumoTotalPercentage(1, $fecha_actual->addDay()->toDateString());
	  
			  $consumo_actual_agua = $this->getConsumoTotalPercentage(2, $fecha); // Tipo de sensor para agua: 2
			  $consumo_anterior_agua = $this->getConsumoTotalPercentage(2, $fecha_actual->addDay()->toDateString());
	  
			  // Calcular el porcentaje de aumento o disminución de consumo respecto al día anterior
			  $porcentaje_electricidad = $this->calcularPorcentaje($consumo_actual_electricidad, $consumo_anterior_electricidad);
			  $porcentaje_agua = $this->calcularPorcentaje($consumo_actual_agua, $consumo_anterior_agua);
	  
			  // Almacenar el porcentaje en el arreglo por día
			  $dia_semana = $fecha_actual->dayName;
			  $porcentajes[$dia_semana] = [
				  'electricidad' => $porcentaje_electricidad,
				  'agua' => $porcentaje_agua,
			  ];
		  }
	  
		  return view('ui.percentage', compact('porcentajes'));
	  }
	  
	  private function getConsumoTotalPercentage($id_tipo_sensor, $fecha)
	  {
		  $consumo_total = Measurement::whereHas('sensor', function ($query) use ($id_tipo_sensor) {
			  $query->where('id_type', '=', $id_tipo_sensor);
		  })->whereDate('fecha', $fecha)
			->sum('consumo');
	  
		  return $consumo_total;
	  }
	  
	  private function calcularPorcentaje($valor_actual, $valor_anterior)
	  {
		  if ($valor_anterior == 0) {
			  return 0; // Evitar división por cero
		  }
	  
		  return (($valor_actual - $valor_anterior) / $valor_anterior) * 100;
	  }
}




