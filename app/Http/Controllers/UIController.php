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

	//FUNCIONES QUE DEVUELVEN VISTAS.

	public function daily()
	{
    // Arreglo para almacenar los datos de consumo por día de la semana
    $consumo_por_dia = [];
		//Recoge el consumo del día de hoy.
		$consumo_actual_electricidad = $this->getConsumoDelDia(1, $this->fecha_aleatoria);
		$consumo_actual_agua = $this->getConsumoDelDia(2, $this->fecha_aleatoria);
    // Recorrer los últimos 7 días
    for ($i = 7; $i > 0; $i--) {
        // Obtener la fecha para el día actual menos $i días
				$fecha_reducida = $this->fecha_aleatoria->copy()->subDays($i);
        $fecha = $fecha_reducida->toDateString();
        // Obtener el consumo del día actual con respecto al de hace $i días
        $consumo_diario_electricidad = $consumo_actual_electricidad - $this->getConsumoDelDia(1, $fecha); // Tipo de sensor para electricidad: 1
        $consumo_diario_agua = $consumo_actual_agua - $this->getConsumoDelDia(2, $fecha); // Tipo de sensor para agua: 2
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
		//Consumo a lo largo de la semana del día actual.
		$consumo_actual_electricidad = $this->getConsumoDeLaSemana(1, $this->fecha_aleatoria->toDateString()); // Tipo de sensor para electricidad: 1
		$consumo_actual_agua = $this->getConsumoDeLaSemana(2, $this->fecha_aleatoria->toDateString()); // Tipo de sensor para agua: 2
		//Recoge el domingo anterior más próximo al día actual, para partir de ahí en la comparación semanal.
		$fecha_inicial_comparacion = DB::select("SELECT MAX(DATE(fecha)) as fecha FROM measurements
		WHERE fecha < ? AND DAYOFWEEK(fecha) = 1", [$this->fecha_aleatoria])[0]->fecha;
		/* 
			Recoge el año, el mes y el día del domingo previamente recogido, para incluirlos en un objeto Carbon que 
			nos permita substraer días cómodamente.
		*/
		$componentes_fecha = explode("-",$fecha_inicial_comparacion);
		$anio = $componentes_fecha[0];
		$mes = $componentes_fecha[1];
		$dia = $componentes_fecha[2];
		$fecha_inicial = Carbon::create($anio, $mes, $dia, 23, 0);
		// Recorrer las 4 semanas previas a la semana anterior (en total, las 5 semanas previas a la semana actual).
		for ($i = 4; $i >= 0; $i--) {
			// Obtener la fecha para el día actual menos 7*$i días
			$fecha_previa = $fecha_inicial->copy()->subDays(7*$i);
			// Obtener el consumo de la semana actual con respecto al de la previa.
			$consumo_comparativo_electricidad = $consumo_actual_electricidad - $this->getConsumoDeLaSemana(1, $fecha_previa->toDateString());
			$consumo_comparativo_agua = $consumo_actual_agua - $this->getConsumoDeLaSemana(2, $fecha_previa->toDateString());
			// Almacenar el consumo en el arreglo.
			$consumo_por_semana[$i] = [
				'electricidad' => $consumo_comparativo_electricidad,
				'agua' => $consumo_comparativo_agua,
			];
		}
		return view('ui.weekly', compact('consumo_por_semana'));
	}
	
  public function monthly()
	{
		//Recoge el mes y el año actuales.
		$mes = $this->fecha_aleatoria->month;
		$anio = $this->fecha_aleatoria->year;
		//Recoge el consumo del mes actual.
		$consumo_actual_electricidad = $this->getConsumoDelMes(1, $anio, $mes);
		$consumo_actual_agua = $this->getConsumoDelMes(2, $anio, $mes);
		//Recorrer todos los meses del año actual.
		for ($i = 1; $i < $mes; $i++){
			//Obtener el consumo del mes actual con respecto al del mes previo ($i).
			$consumo_mensual_electricidad = $consumo_actual_electricidad - $this->getConsumoDelMes(1, $anio, $i);
			$consumo_mensual_agua = $consumo_actual_agua - $this->getConsumoDelMes(2, $anio, $i);
			// Almacenar el consumo en el arreglo.
			$consumo_por_mes[$i - 1] = [
				'electricidad' => $consumo_mensual_electricidad,
				'agua' => $consumo_mensual_agua
			];
		}
   return view('ui.monthly', compact('consumo_por_mes'));
	}
  
	public function current_day()
	{
		// Obtener las medidas de consumo de electricidad y agua para el día actual.
		$consumo_por_hora_electricidad = $this->getConsumoPorHora(1, $this->fecha_aleatoria->toDateString()); // Tipo de sensor para electricidad: 1
		$consumo_por_hora_agua = $this->getConsumoPorHora(2, $this->fecha_aleatoria->toDateString()); // Tipo de sensor para agua: 2
		//Retornar las medidas.
		return view('ui.currentday', compact('consumo_por_hora_electricidad', 'consumo_por_hora_agua'));
	}

	//FUNCIONES QUE DEVUELVEN DATOS A LAS FUNCIONES DE VISTAS.

	private function getConsumoPorHora($id_tipo_sensor, $fecha)
	{
		//Consulta que almacena en un array el tipo de sensor pasado como parámetro.
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		//Recoger los registros de cada hora del día correspondiente a la fecha pasada como parámetro.
		$consumo_por_hora = DB::table('measurements')
		->select(DB::raw("consumo"))
		->whereIn('id_sensor', collect($sensor_type)->pluck('id_sensor')->toArray())
		->whereRaw('DATE(fecha) = ?', [$fecha])
		->orderBy("consumo")
		->pluck("consumo")
		->toArray();
		/* 
		Recoger el valor del registro inicial y restárselo a todos los registros previamente recogidos para 
		obtener el consumo real del día por hora (como si fuese la primera vez en la vida de la app que se
		registra el consumo). 
		*/
		$consumo_inicial = $consumo_por_hora[0];
		for ($i = 0; $i < sizeof($consumo_por_hora); $i++){
			$consumo_por_hora[$i] -= $consumo_inicial;
		}
		return $consumo_por_hora;
	}

	private function getConsumoDelDia($id_tipo_sensor, $fecha)
	{
		//Consulta que almacena en un array el tipo de sensor pasado como parámetro.
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		// Recoger las medidas del día de la fecha pasada como parámetro
		$query = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereRaw("DATE(fecha) = ?", [$fecha])->orderBy("consumo")->pluck("consumo");
		//Recoger la última medida.
		$ultimo_registro_dia = $query->get($query->count() - 1);
		//Recoger la primera medida.
		$primer_registro_dia = $query->get(0);
		/*
		La resta entre el último y primer registro de los respectivos días da como resultado el consumo real 
		de esos días.
		*/
		return $ultimo_registro_dia - $primer_registro_dia;
	}

	private function getConsumoDeLaSemana($id_tipo_sensor, $fecha)
	{
		//Consulta que almacena en un array el tipo de sensor pasado como parámetro.
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		//Recoger el anterior lunes más próximo a la fecha pasada como parámetro.
		$first_fecha = DB::select("SELECT DISTINCT fecha AS closest_monday FROM measurements WHERE HOUR(fecha) = 0 
		AND DATE(fecha) = (SELECT MAX(DATE(fecha)) FROM measurements WHERE fecha < ? AND DAYOFWEEK(fecha) 
		= 2)", [$fecha])[0]->closest_monday;
		// Recoger el último registro correspondiente a la fecha pasada como parámetro (la última medición de ese día).
		$last_fecha = DB::select("SELECT DISTINCT fecha FROM measurements WHERE DATE(fecha) = ? 
		ORDER BY fecha DESC LIMIT 1", [$fecha])[0]->fecha;
		//Recoger los dos consumos correspondientes a las fechas última y primera para luego devolver la diferencia.
		$datos_consumo = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereIn("fecha", function ($query) use ($first_fecha, $last_fecha) {
			$query->select("fecha")->from("measurements")
			->where("fecha", "=", $first_fecha)
			->orWhere("fecha", "=", $last_fecha);
		})
		->orderBy("consumo")
		->pluck("consumo")
		->toArray();
		return $datos_consumo[1] - $datos_consumo[0];
	}

	private function getConsumoDelMes($id_tipo_sensor, $anio, $mes)
	{
		//Consulta que almacena en un array el tipo de sensor pasado como parámetro.
		$sensor_type = DB::select("SELECT id_sensor FROM sensors WHERE id_type=$id_tipo_sensor");
		// Recoger la primera medida del mes pasado como parámetro (teniendo en cuenta el año).
		$primer_registro_mes = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereYear("fecha", "=", $anio)->whereMonth("fecha", "=", $mes)->orderBy("consumo")->limit(1)
		->pluck("consumo")->get(0);
		// Recoger la última medida del mes pasado como parámetro (teniendo en cuenta el año).
		$ultimo_registro_mes = DB::table("measurements")->select(DB::raw("consumo"))
		->whereIn("id_sensor", collect($sensor_type)->pluck("id_sensor")->toArray())
		->whereYear("fecha", "=", $anio)->whereMonth("fecha", "=", $mes)->orderByDesc("consumo")->limit(1)
		->pluck("consumo")->get(0);
		/*
		La resta entre el último y primer registro de los respectivos meses da como resultado el consumo real 
		de esos meses.
		*/
		return $ultimo_registro_mes - $primer_registro_mes;
	}

}