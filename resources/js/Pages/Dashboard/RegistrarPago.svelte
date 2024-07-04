<script>
    import axios from "axios";
    import debounce from "lodash/debounce";
    import BalanceBar from "../../components/BalanceBar.svelte";
    import { useForm } from "@inertiajs/svelte";

    import { getMonitor } from "consulta-dolar-venezuela";
    const currentDate = new Date();
    const currentDateString = currentDate.toISOString().split("T")[0];
    export let data;
    console.log({ data });
    let dolarPrice;
    let formCreate = useForm({
        date: currentDateString,
        name: "Fabian",
        rep_id: null,
        currency: "Bolivar",
        payment_method: "",
        amount: "",
        bs: "",
        vaucher: "1234568",
    });

    getMonitor("BCV", "lastUpdate")
        .then((response) => {
            dolarPrice = response.bcv.price;
            console.log(dolarPrice);
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    $: console.log($formCreate);
    let representatives = [];

    const search_rep1 = debounce(async (search) => {
        if (search.length == 0) {
            representatives = [];
        } else {
            try {
                const response = await axios.get(
                    `/dashboard/pagos/search-representative/${search}`,
                );
                representatives = response.data;
                console.log(representatives);
                console.log(response);
            } catch (error) {
                console.log(error);
            }
        }
    }, 300);
</script>

<svelte:head>
    <title>Registrar Pago</title>
</svelte:head>

<div class="relative flex items-center mt-4 md:mt-0">
    <span class="absolute">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-5 h-5 mx-3 text-gray-400"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
            />
        </svg>
    </span>

    <input
        on:input={(e) => {
            search_rep1(e.target.value);
        }}
        type="search"
        placeholder="Buscar representante"
        class="block w-full py-1.5 pr-5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg md:w-80 placeholder-gray-400/70 pl-11 rtl:pr-11 rtl:pl-5 focus:border-blue-400 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
    />
</div>
{#if representatives.length > 0}

<table
    class="border p-2 bg-gray-100 mt-1 min-w-[322px] rounded-b-md overflow-hidden shadow"
>

    <thead>
        <tr class="bg-gray-200 text-dark">
            <th class="px-2 text-left">Nombre completo</th>
            <th class="px-2 text-left">Cedula</th>
        </tr>
    </thead>
        <tbody>
            {#each representatives as representative}
                <tr
                    class="hover:bg-color3 cursor-pointer hover:text-gray-100 border-b"
                    on:click={() => {
                        $formCreate.rep_id = representative?.id;
                        $formCreate.full_name = representative?.name + " " + representative?.last_name;
                        representatives = []
                    }}
                >
                    <td class="px-2 text-left py-2"
                        >{representative?.name} {representative?.last_name}</td
                    >
                    <td class="px-2 text-left py-2">{representative?.ci}</td>
                </tr>
            {/each}
        </tbody>
    </table>
    {/if}

{#if $formCreate.rep_id}
    <section class="mt-4">
        <h2>
            <span class="color1">Representante: </span>
            <b class="text-color1"> { $formCreate.full_name} </b>
        </h2>

        <table class="w-full">
            <thead>
                <tr class="[&_th]:px-2 w-full">
                    <th class="text-left pl-0">Representados:</th>
                    <th class="">Mensualidades:</th>
                    <th class="text-right">Deuda:</th>
                    <th class="text-right">Pagar ($)</th>
                    <th class="text-right">Pagar (Bs)</th>
                    <th class="text-right">Deuda restante</th>
                </tr>
            </thead>
            <tbody>
                <tr class="[&_td]:px-2 w-full [&_td]:pb-2">
                    <td class="pl-0">Clark kent Villasmil</td>
                    <td class="">
                        <BalanceBar
                            balance={{
                                lastPaidMonth: 2,
                                lastPartiallyPaidMonth: 3,
                                lastOverdueMonth: 6,
                                paying: 5,
                            }}
                        />
                    </td>
                    <td class="text-right">180$</td>
                    <td
                        ><input
                            class="max-w-[100px] py-1 text-right font-bold border border-color1 rounded-lg"
                            type="number"
                            value="40"
                        /></td
                    >
                    <td
                        ><input
                            class="max-w-[100px] py-1 text-right font-bold border border-color1 rounded-lg"
                            type="number"
                            value="1.520"
                        /></td
                    >
                    <td class="text-right">140$</td>
                </tr>

                <tr class="[&_td]:px-2 w-full [&_td]:pb-2">
                    <td class="pl-0">Bruce Wayne Villasmil</td>
                    <td class="">
                        <BalanceBar
                            balance={{
                                lastPaidMonth: 4,
                                lastPartiallyPaidMonth: 3,
                                lastOverdueMonth: 6,
                                paying: 7,
                            }}
                        />
                    </td>
                    <td class="text-right">180$</td>
                    <td
                        ><input
                            class="max-w-[100px] py-1 text-right font-bold border border-color1 rounded-lg"
                            type="number"
                            value="40"
                        /></td
                    >
                    <td
                        ><input
                            class="max-w-[100px] py-1 text-right font-bold border border-color1 rounded-lg"
                            type="number"
                            value="1.520"
                        /></td
                    >
                    <td class="text-right">140$</td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-300 rounded-lg">
                <tr class="[&_td]:px-2 [&_td]:py-1.5 [&_td]:text-right">
                    <td colspan="2" class="text-right text-color1 font-semibold"
                        >TOTALES:</td
                    >
                    <td class="">50$</td>
                    <td
                        ><input
                            class="max-w-[100px] py-1 text-right font-bold border border-color1 rounded-lg"
                            type="number"
                            bind:value={$formCreate.amount}
                            error={$formCreate.errors?.amount}
                            on:input={(e) => {
                                $formCreate.bs = (
                                    e.target.value * dolarPrice
                                ).toFixed(2);
                            }}
                        /></td
                    >
                    <td class="">
                        <input
                            class="max-w-[100px] py-1 text-right font-bold border border-color1 rounded-lg"
                            type="number"
                            value="5.000"
                        />
                    </td>
                    <td>40$</td>
                </tr>
            </tfoot>
        </table>
    </section>
    <section class="mt-4">
        <label>
            <span>Método de pago</span>
            <select
                class="block w-44 p-3 py-1 text-left font-bold border border-color1 rounded-lg"
                name=""
                id=""
            >
                <option value="1">Pago movil</option>
            </select>
        </label>
        <div class="flex gap-3 mt-5">
            <article
                id={`account-$23`}
                class={`rounded-md bg-white border-l-8 border-color3 pb-5 pt-3 md:px-8`}
            >
                <header class="flex justify-between">
                    <h3 class="text-color1 font-semibold">Pago Movil</h3>
                </header>
                <div class="grid grid-cols-3 justify-items-start gap-4 py-2">
                    <div>
                        <h4 class="text-gray-500">Banco:</h4>
                        <p>Banesco</p>
                    </div>

                    <div>
                        <h4 class="text-gray-500">Teléfono:</h4>
                        <p>04124393123</p>
                    </div>

                    <div>
                        <h4 class="text-gray-500">Cédula:</h4>
                        <p>27253194</p>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section class="flex gap-4 mt-3">
        <label>
            <span>Fecha:</span>
            <input
                class="block w-44 p-3 py-1 text-left font-bold border border-color1 rounded-lg"
                type="date"
                name=""
                id=""
            />
        </label>
        <label>
            <span>Referencia:</span>
            <input
                class="block w-44 p-3 py-1 text-left font-bold border border-color1 rounded-lg"
                type="text"
                name=""
                id=""
            />
        </label>
    </section>
{/if}

<style>
    /* .progress_balance span {
    } */
</style>
