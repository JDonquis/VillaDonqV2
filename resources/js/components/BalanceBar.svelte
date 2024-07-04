<script>
export let balance = {}
console.log(balance);
</script>

<div
class="progress_balance text-sm relative grid grid-cols-12 rounded-full py-2  border border-color1"
>
<span class="px-2 opacity-0">Ene</span>
<span class="px-2 opacity-0">Feb</span>
<span class="px-2 opacity-0">Mar</span>
<span class="px-2 opacity-0">Abr</span>
<span class="px-2 opacity-0">May</span>
<span class="px-2 opacity-0">Jun</span>
<span class="px-2 opacity-0">Jul</span>
<span class="px-2 opacity-0">Ago</span>
<span class="px-2 opacity-0">Sep</span>
<span class="px-2 opacity-0 border-color4">Oct</span>
<span class="px-2 opacity-0">Nov</span>
<span class="px-2 opacity-0">Dic</span>

<div
    class=" left-0 bottom-0 absolute w-full h-full grid grid-cols-12 rounded-full overflow-hidden"
>
    <span class="px-2 col-span-12 z-50 h-full rounded-l-full bg-white bg-opacity-30 "
    ></span>
</div>


<div
    class={`left-0 bottom-0 absolute w-full h-full grid grid-cols-12 rounded-full overflow-hidden`}
>
    <span class={`px-2 z-50 h-full rounded-l-full bg-color4 bg-opacity-20 border-4  border-color4`}
    style={`grid-column: span ${balance?.paying} `}

    ></span>
</div>
<div
    class={`left-0 bottom-0 absolute w-full h-full grid grid-cols-12 rounded-full overflow-hidden`}
>
    <span class={`px-2 z-40 h-full rounded-ful bg-color3`}
    style={`grid-column: span ${balance?.lastPaidMonth}`}

    ></span>
</div>
<div
    class={`left-0 bottom-0 absolute w-full h-full grid grid-cols-12 rounded-full overflow-hidden`}
>
    <span class={`px-2 z-30 h-full rounded-ful bg-binance`}
    style={`grid-column: span ${balance?.lastPartiallyPaidMonth}`}

    ></span>
</div>
<div
    class={`left-0 bottom-0 absolute w-full h-full grid grid-cols-12 rounded-full overflow-hidden`}
>
    <span class={`px-2  z-20 h-full rounded-ful bg-red`}
    style={`grid-column: span ${balance?.lastOverdueMonth}`}

    ></span>
</div>


<div
    class=" left-0 text-center font-bold bottom-0 text-sm absolute bg-white w-full grid grid-cols-12 rounded-full py-2"
>
    <span class="px-2 z-50">Ene</span>
    <span class="px-2 z-50">Feb</span>
    <span class="px-2 z-50">Mar</span>
    <span class="px-2 z-50">Abr</span>
    <span class="px-2 z-50">May</span>
    <span class="px-2 z-50">Jun</span>
    <span class="px-2 z-50">Jul</span>
    <span class="px-2 z-50">Ago</span>
    <span class="px-2 z-50">Sep</span>
    <span class="px-2 z-50">Oct</span>
    <span class="px-2 z-50">Nov</span>
    <span class="px-2 z-50">Dic</span>
</div>
</div>