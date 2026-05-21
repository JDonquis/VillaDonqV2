<script>
  import { onMount, onDestroy } from 'svelte';
  import * as echarts from 'echarts';

  let chartContainer;
  let myChart;

  // 1. SUPONGAMOS QUE ESTOS SON LOS DATOS CRUDOS QUE LLEGAN DE TU ENDPOINT
  // (Convertimos strings a números y "" a null para que la matemática no falle)
  const pagadoMensual = [4500, 4500, 4000, 2000, 6500, 4800, null, null, null, null];
  const esperadoMensual = [5000, 5000, 5000, 5000, 5000, 5000, 5000, 5000, 5000, 5000];
  const ingresoRealAcumulado = [4500, 9000, 13000, 15000, 21500, 26300, null, null, null, null];
  const metaEsperadaAcumulada = [5000, 10000, 15000, 20000, 25000, 30000, 35000, 40000, 45000, 50000];

  // 2. FUNCIÓN MATEMÁTICA PARA CALCULAR EL TOPE PERFECTO (Múltiplo de 5 para los saltos del eje)
  function calcularTopeEje(arraysCombinados) {
    // Filtramos nulls, vacíos o cosas que no sean números y buscamos el valor más alto
    const maxValor = Math.max(...arraysCombinados.flat().map(v => Number(v)).filter(v => !isNaN(v)));
    
    if (maxValor <= 0) return 5000; // Valor por defecto si no hay datos

    // Añadimos un 10% de margen superior para que las barras/líneas no toquen el techo del gráfico
    const valorConMargen = maxValor * 1.1; 

    // Buscamos el próximo número más alto que sea divisible exactamente entre 5
    // Esto garantiza que al dividir el eje en 5 tramos (interval), den números enteros limpios
    return Math.ceil(valorConMargen / 5) * 5;
  }

  // 3. CÁLCULO REACTIVO DE LOS TOPES
  // Evaluamos tanto lo real como lo esperado para asegurar que nada se desborde
  $: maxMensual = calcularTopeEje([pagadoMensual, esperadoMensual]);
  $: maxAcumulado = calcularTopeEje([ingresoRealAcumulado, metaEsperadaAcumulada]);

  // 4. EL OBJETO OPTION SE CONFIGURA DINÁMICAMENTE
  // Usamos una declaración reactiva ($:) para que si los datos cambian, el gráfico se entere
  $: option = {
    color: ['#88d498', '#dddddd', '#1f4287', '#ff6b6b'],
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'cross', crossStyle: { color: '#999' } }
    },
    toolbox: {
      feature: { dataView: { show: true, readOnly: true, title: 'Ver Datos' } }
    },
    legend: {
      data: ['Pagado Mensual', 'Esperado Mensual', 'Ingreso Real Acumulado', 'Meta Esperada Acumulada'],
      bottom: 0
    },
    xAxis: [
      {
        type: 'category',
        data: ['Sep', 'Oct', 'Nov', 'Dic', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        axisPointer: { type: 'shadow' }
      }
    ],
    yAxis: [
      {
        type: 'value',
        name: 'Flujo Mensual',
        min: 0,
        max: maxMensual,
        interval: maxMensual / 5, // División perfecta en 5 partes
        axisLabel: { formatter: '${value}' }
      },
      {
        type: 'value',
        name: 'Histórico Anual',
        min: 0,
        max: maxAcumulado,
        interval: maxAcumulado / 5, // División perfecta en 5 partes
        axisLabel: { formatter: '${value}' },
        splitLine: { show: false } 
      }
    ],
    series: [
      {
        name: 'Pagado Mensual',
        type: 'bar',
        tooltip: { valueFormatter: (value) => '$' + (value ? value.toLocaleString() : 0) },
        data: pagadoMensual
      },
      {
        name: 'Esperado Mensual',
        type: 'bar',
        tooltip: { valueFormatter: (value) => '$' + value.toLocaleString() },
        data: esperadoMensual
      },
      {
        name: 'Ingreso Real Acumulado',
        type: 'line',
        yAxisIndex: 1, 
        smooth: true,
        tooltip: { valueFormatter: (value) => '$' + (value ? value.toLocaleString() : 0) },
        data: ingresoRealAcumulado
      },
      {
        name: 'Meta Esperada Acumulada',
        type: 'line',
        yAxisIndex: 1, 
        smooth: true,
        lineStyle: { type: 'dashed', width: 2 },
        tooltip: { valueFormatter: (value) => '$' + value.toLocaleString() },
        data: metaEsperadaAcumulada
      }
    ]
  };

  // 5. OBSERVAR CAMBIOS EN OPTION PARA ACTUALIZAR EL GRÁFICO
  // Si los datos llegan después de que el componente montó (frecuente con fetch), esto redibuja automáticamente
  $: if (myChart && option) {
    myChart.setOption(option);
  }

  function handleResize() {
    if (myChart) myChart.resize();
  }

  onMount(() => {
    myChart = echarts.init(chartContainer);
    myChart.setOption(option);
    window.addEventListener('resize', handleResize);
  });

  onDestroy(() => {
    if (myChart) myChart.dispose();
    window.removeEventListener('resize', handleResize);
  });
</script>

<div class="w-full bg-white p-6 border-4 large-shadow border-black max-w-[1200px] flex flex-col gap-4">
  <div>
  <div class="flex">
    
    <h3 class="text-lg font-bold text-gray-800 tracking-tight">Recaudación Anual vs. Flujo Mensual</h3>
      <Input
            type="select"
            required={true}
            label={"Periodo Escolar"}
            bind:value={$formReinscribe.course_id}
            error={$formReinscribe.errors?.course_id}
            disabled={submitStatus == "Editar"}
        >
            {#each data.courses as course}
                <option value={course.id}>{course.name}</option>
            {/each}
        </Input>
</div>
    <p class="text-xs text-gray-400">Análisis acumulativo del año escolar en curso</p>
  </div>

  <div bind:this={chartContainer} class="w-full h-[400px]"></div>
</div>