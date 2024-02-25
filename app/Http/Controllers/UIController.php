<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Measurement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UIController extends Controller
{
	private $fecha_aleatoria;
	
	public function __construct() {
		$this->fecha_aleatoria = Carbon::create(2021, 3, 24);
	}
	/**
	 * La función "daily" recupera y almacena datos de consumo diario de electricidad y agua durante la
	 * última semana para mostrarlos en una vista.
	 * 
	 * @return La función `daily()` está devolviendo una vista llamada 'ui.daily' con los datos
	 * almacenados en el array `$consumo_por_dia`. Esta matriz contiene los datos de consumo de los
	 * últimos 7 días desglosados por día de la semana y tipo de sensor (electricidad y agua).
	 */
	public function daily()
	{
    // Arreglo para almacenar los datos de consumo por día de la semana
    $consumo_por_dia = [];
    // Recorrer los últimos 7 días
    for ($i = 7; $i > 0; $i--) {
        // Obtener la fecha para el día actual menos $i días
				$fecha_reducida = $this->fecha_aleatoria->copy()->subDays($i);
        $fecha = $fecha_reducida->toDateString();
        // Obtener el consumo del día actual con respecto al de hace $i días
        $consumo_diario_electricidad = $this->getConsumoDelDia(1, $fecha); // Tipo de sensor para electricidad: 1
        $consumo_diario_agua = $this->getConsumoDelDia(2, $fecha); // Tipo de sensor para agua: 2
        // Almacenar los datos en el arreglo por día de la semana
        $dia_semana = $fecha_reducida->dayName;
        $consumo_por_dia[$dia_semana] = [
            'agua' => $consumo_diario_agua,
            'electricidad' => $consumo_diario_electricidad,
        ];
    }
    return view('ui.daily', compact('consumo_por_dia'));
	}
	
  public function weekly()
	{
		// // Obtener fecha.
		// $inicio_2020 = Carbon::create(2020, 2, 1); // 1 de enero de 2020
		// $final_2020 = Carbon::create(2020, 7, 31);   // 31 de julio de 2020
		// $fecha = Carbon::createFromTimestamp(mt_rand($inicio_2020->timestamp,$final_2020->timestamp));
		// //Consumo de la semana actual.
		// $consumo_actual_electricidad = $this->getConsumoDeLaSemana(1, $fecha->toDateString()); // Tipo de sensor para electricidad: 1
		// $consumo_actual_agua = $this->getConsumoDeLaSemana(2, $fecha->toDateString()); // Tipo de sensor para agua: 2
		// // Arreglo para almacenar los porcentajes de aumento o disminución por semana.
		// $porcentajes = [];
		// // Recorrer las 5 semanas previas a la semana anterior.
		// for ($i = 6; $i >= 2; $i--) {
		// 	// Obtener la fecha para el día actual menos 7*$i días
		// 	$fecha_previa = $fecha->copy()->subDays(7*$i)->toDateString();
		// 	// Obtener el consumo por hora para la semana actual y la semana anterior.
		// 	$consumo_anterior_electricidad = $this->getConsumoDeLaSemana(1, $fecha->addDay()->toDateString());
		// 	$consumo_anterior_agua = $this->getConsumoDeLaSemana(2, $fecha->addDay()->toDateString());
	
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
		$mes = $this->fecha_aleatoria->month;
		$anio = $this->fecha_aleatoria->year;
		for ($i = 1; $i < $mes; $i++){
			$consumo_mensual_electricidad = $this->getConsumoDelMes(1, $anio, $i);
			$consumo_mensual_agua = $this->getConsumoDelMes(2, $anio, $i);
			$consumo_por_mes[$i - 1] = [
				'electricidad' => $consumo_mensual_electricidad,
				'agua' => $consumo_mensual_agua
			];
		}
   return view('ui.monthly', compact('consumo_por_mes'));
	}
  
	public function current_day()
	{
		// Obtener las medidas de consumo de electricidad y agua para el día actual
		$consumo_por_hora_electricidad = $this->getConsumoPorHora(1, $this->fecha_aleatoria->toDateString()); // Tipo de sensor para electricidad: 1
		$consumo_por_hora_agua = $this->getConsumoPorHora(2, $this->fecha_aleatoria->toDateString()); // Tipo de sensor para agua: 2
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
	
	private function getConsumoDelDia($id_tipo_sensor, $fecha_relativa)
	{
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");

		$ultimo_registro_dia_previo = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())->
		whereRaw("DATE(fecha) = ?", [$fecha_relativa])->orderByDesc("consumo")->limit(1)->pluck("consumo")
		->get(0);
		$primer_registro_dia_previo = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())->
		whereRaw("DATE(fecha) = ?", [$fecha_relativa])->orderBy("consumo")->limit(1)->pluck("consumo")
		->get(0);
		$ultimo_registro_dia_actual = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereRaw("DATE(fecha) = ?", [$this->fecha_aleatoria->toDateString()])->orderByDesc("consumo")
		->limit(1)->pluck("consumo")->get(0);
		$primer_registro_dia_actual = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereRaw("DATE(fecha) = ?", [$this->fecha_aleatoria->toDateString()])->orderBy("consumo")
		->limit(1)->pluck("consumo")->get(0);
		$consumo_dia_actual = $ultimo_registro_dia_actual - $primer_registro_dia_actual;
		$consumo_dia_previo = $ultimo_registro_dia_previo - $primer_registro_dia_previo;
		return $consumo_dia_actual - $consumo_dia_previo;
	}
	private function getConsumoDelMes($id_tipo_sensor, $anio, $mes)
	{
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		$ultimo_registro_mes_actual = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereYear("fecha", "=", $this->fecha_aleatoria->year)
		->whereMonth("fecha", "=", $this->fecha_aleatoria->month)->orderByDesc("consumo")->limit(1)
		->pluck("consumo")->get(0);
		$primer_registro_mes_actual = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereYear("fecha", "=", $this->fecha_aleatoria->year)
		->whereMonth("fecha", "=", $this->fecha_aleatoria->month)->orderBy("consumo")->limit(1)
		->pluck("consumo")->get(0);
		$primer_registro_mes_previo = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereYear("fecha", "=", $anio)->whereMonth("fecha", "=", $mes)->orderBy("consumo")->limit(1)
		->pluck("consumo")->get(0);
		$ultimo_registro_mes_previo = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereYear("fecha", "=", $anio)->whereMonth("fecha", "=", $mes)->orderByDesc("consumo")->limit(1)
		->pluck("consumo")->get(0);
		$consumo_mes_actual = $ultimo_registro_mes_actual - $primer_registro_mes_actual;
		$consumo_mes_previo = $ultimo_registro_mes_previo - $primer_registro_mes_previo;
		return $consumo_mes_actual - $consumo_mes_previo;
	}

	private function getConsumoDeLaSemana($id_tipo_sensor, $fecha)
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


	private function calcularPorcentaje($valor_actual, $valor_anterior)
	{
		if ($valor_anterior == 0) 
			return 0; // Evitar división por cero
		return (($valor_actual - $valor_anterior) / $valor_anterior) * 100;
	}
}




