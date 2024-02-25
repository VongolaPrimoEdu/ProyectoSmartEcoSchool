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

	}
	
  public function weekly()
	{	
		//Código para representar días ficticios. Eliminar cuando no haga falta.
		$inicio_2020 = Carbon::create(2020, 2, 1); // 1 de enero de 2020
		$final_2020 = Carbon::create(2020, 7, 31);   // 31 de julio de 2020
		$fecha_aleatoria = Carbon::createFromTimestamp(mt_rand($inicio_2020->timestamp,$final_2020->timestamp));

    // Obtener la fecha actual
    // $fecha_actual = Carbon::now();

    // Arreglo para almacenar los datos de consumo por día de la semana
    $consumo_por_dia = [];
		//la fecha es 2020-04-16
    // Recorrer los últimos 7 días
    for ($i = 7; $i > 0; $i--) {
        // Obtener la fecha para el día actual menos $i días
				$fecha_reducida = $fecha_aleatoria->copy()->subDays($i);
        $fecha = $fecha_reducida->toDateString();

        // Obtener los datos de consumo por hora para esa fecha
        $consumo_por_hora_electricidad = $this->getConsumoDelDia(1, $fecha); // Tipo de sensor para electricidad: 1
        $consumo_por_hora_agua = $this->getConsumoDelDia(2, $fecha); // Tipo de sensor para agua: 2

        // Sumar el consumo total por hora para obtener el consumo diario
        $consumo_diario_electricidad = $consumo_por_hora_electricidad;
        $consumo_diario_agua = $consumo_por_hora_agua;

        // Almacenar los datos en el arreglo por día de la semana
        $dia_semana = $fecha_reducida->dayName;
        $consumo_por_dia[$dia_semana] = [
            'agua' => $consumo_diario_agua,
            'electricidad' => $consumo_diario_electricidad,
        ];
    }
    return view('ui.weekly', compact('consumo_por_dia'));
		// // Obtener fecha.
		// $inicio_2020 = Carbon::create(2020, 2, 1); // 1 de enero de 2020
		// $final_2020 = Carbon::create(2020, 7, 31);   // 31 de julio de 2020
		// $fecha = Carbon::createFromTimestamp(mt_rand($inicio_2020->timestamp,$final_2020->timestamp));
		// //Consumo de la semana actual.
		// $consumo_actual_electricidad = $this->getConsumoTotalSemana(1, $fecha->toDateString()); // Tipo de sensor para electricidad: 1
		// $consumo_actual_agua = $this->getConsumoTotalSemana(2, $fecha->toDateString()); // Tipo de sensor para agua: 2
		// // Arreglo para almacenar los porcentajes de aumento o disminución por semana.
		// $porcentajes = [];
		// // Recorrer las 5 semanas previas a la semana anterior.
		// for ($i = 6; $i >= 2; $i--) {
		// 	// Obtener la fecha para el día actual menos 7*$i días
		// 	$fecha_previa = $fecha->copy()->subDays(7*$i)->toDateString();
		// 	// Obtener el consumo por hora para la semana actual y la semana anterior.
		// 	$consumo_anterior_electricidad = $this->getConsumoTotalSemana(1, $fecha->addDay()->toDateString());
		// 	$consumo_anterior_agua = $this->getConsumoTotalSemana(2, $fecha->addDay()->toDateString());
	
		// 	// Calcular el porcentaje de aumento o disminución de consumo respecto al día anterior
		// 	$porcentaje_electricidad = $this->calcularPorcentaje($consumo_actual_electricidad, $consumo_anterior_electricidad);
		// 	$porcentaje_agua = $this->calcularPorcentaje($consumo_actual_agua, $consumo_anterior_agua);
	
		// 	// Almacenar el porcentaje en el arreglo por día
		// 	$dia_semana = $fecha->dayName;
		// 	$porcentajes[$dia_semana] = [
		// 		'electricidad' => $porcentaje_electricidad,
		// 		'agua' => $porcentaje_agua,
		// 	];
		// }
	
		// return view('ui.currentday', compact('porcentajes'));
	}
	
  public function monthly()
	{
		//Iteramos por el año y mientras haya meses, seguimos registrando en el array.
		
   	// Obtener el año y mes actual
		$inicio_2020 = Carbon::create(2020, 2, 1); // 1 de enero de 2020
		$final_2020 = Carbon::create(2020, 7, 31);   // 31 de julio de 2020
		$fecha_aleatoria = Carbon::createFromTimestamp(mt_rand($inicio_2020->timestamp,$final_2020->timestamp));
		$anio = $fecha_aleatoria->year;
		$meses = $meses = DB::table('measurements')
            ->select(DB::raw('DISTINCT MONTH(fecha) as month'))
            ->whereYear('fecha', $anio)
            ->orderBy('month')
            ->pluck('month');

		foreach ($meses as $mes) {
			// Obtener el consumo total de electricidad y agua para el mes actual
			$consumo_total_electricidad = $this->getConsumoTotal(1, $anio, $mes); // Tipo de sensor para electricidad: 1
			$consumo_total_agua = $this->getConsumoTotal(2, $anio, $mes); // Tipo de sensor para agua: 2
			$consumo_por_mes[$mes - 1] = [
				'electricidad' => $consumo_total_electricidad,
				'agua' => $consumo_total_agua
			];
		}
   return view('ui.monthly', compact('consumo_por_mes'));
	}
  
	public function current_day()
	{
		$inicio_2020 = Carbon::create(2020, 2, 1); // 1 de enero de 2020
		$final_2020 = Carbon::create(2020, 7, 31);   // 31 de julio de 2020
		$fecha_aleatoria = Carbon::createFromTimestamp(mt_rand($inicio_2020->timestamp,$final_2020->timestamp))->toDateString();
	
		// Obtener las medidas de consumo de electricidad y agua para el día actual
		$consumo_por_hora_electricidad = $this->getConsumoPorHora(1, $fecha_aleatoria); // Tipo de sensor para electricidad: 1
		$consumo_por_hora_agua = $this->getConsumoPorHora(2, $fecha_aleatoria); // Tipo de sensor para agua: 2
		return view('ui.currentday', compact('consumo_por_hora_electricidad', 'consumo_por_hora_agua'));
	}

	  
	private function getConsumoPorHora($id_tipo_sensor, $fecha)
	{
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		$consumo_por_hora = DB::table('measurements')
		->select(DB::raw("consumo"))
		->whereIn('id_sensor', collect($sensor_type)->pluck('id_sensor')->toArray())
		->whereRaw('DATE(fecha) = ?', [$fecha])
		->orderBy("consumo")
		->pluck("consumo")
		->toArray();
		$consumo_inicial = $consumo_por_hora[0];
		for ($i = 0; $i < sizeof($consumo_por_hora); $i++){
			$consumo_por_hora[$i] -= $consumo_inicial;
		}
		return $consumo_por_hora;
	}

	private function getConsumoDelDia($id_tipo_sensor, $fecha)
	{
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		$consumo_del_dia = DB::table('measurements')
		->select(DB::raw("consumo"))
		->whereIn('id_sensor', collect($sensor_type)->pluck('id_sensor')->toArray())
		->whereRaw('DATE(fecha) = ?', [$fecha])
		->orderByDesc('consumo')
		->limit(1)
		->pluck('consumo')
		->get(0);
		return $consumo_del_dia;
	}

	private function getConsumoTotalSemana($id_tipo_sensor, $fecha)
	{
		$date = date_create($fecha);
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		$consumo_semanal = DB::table('measurements')
		->select(DB::raw('SUM(consumo)'))
		->whereIn('id_sensor', collect($sensor_type)->pluck('id_sensor')->toArray())
		->whereBetween("fecha",[date_diff($date, ), $fecha])
		->pluck('SUM(consumo)')
		->toArray()[0];
		return $consumo_semanal;
	}

	private function getConsumoTotal($id_tipo_sensor, $anio, $mes)
	{
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		$consumo_total = DB::table('measurements')
		->select(DB::raw('SUM(consumo)'))
		->whereIn('id_sensor', collect($sensor_type)->pluck('id_sensor')->toArray())
		->whereYear("fecha", "=", $anio)
		->whereMonth("fecha", "=", $mes)
		->pluck("SUM(consumo)")
		->toArray()[0];
		return $consumo_total;
	}

	private function calcularPorcentaje($valor_actual, $valor_anterior)
	{
		if ($valor_anterior == 0) 
			return 0; // Evitar división por cero
		return (($valor_actual - $valor_anterior) / $valor_anterior) * 100;
	}
}




