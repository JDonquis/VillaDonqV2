<script>
    export let balances = {};
    const months = {
        sep: "september",
        oct: "october",
        nov: "november",
        dic: "december",
        ene: "january",
        feb: "february",
        mar: "march",
        abr: "april",
        may: "may",
        jun: "june",
        jul: "july",
        ago: "august",
    };

    export let amountToPay = 0;
    const firstUnpaidMonth = Object.entries(months).findIndex(
        ([spanisMonth, monthName]) => {
            const status = balances[0][`${monthName}_status`];
            return status === "debt" || status === "partially_paid";
        },
    );

    const startPointToPay = {
        school_lapse_index: 0,
        month: firstUnpaidMonth || Object.values(months)[0], // Si no hay deudas, cae al primer mes por defecto
    };

    let endPointToPay = {};
    console.log(amountToPay);

    function getLastPaymentMonth(amountToPay) {
        let lastPaymentMonth = null;
        let endMonthIndex = firstUnpaidMonth;
        let endYearIndex = startPointToPay.school_lapse_index;
        let partialToPay = 0;
        while (amountToPay > 0) {
            const balance = Math.abs(
                balances[endYearIndex][Object.values(months)[endMonthIndex]],
            );

            if (amountToPay < balance) {
                partialToPay = amountToPay;
            }

            amountToPay -= balance;

            if (endMonthIndex == 12) {
                endYearIndex++;
                endMonthIndex = 0;
            } else {
                endMonthIndex++;
            }
            console.log({
                endMonthIndex,
                endYearIndex,
                partialToPay,
                amountToPay,
                balance,
            });
        }
        endPointToPay = { endMonthIndex, endYearIndex, partialToPay };
        return { endMonthIndex, endYearIndex, partialToPay };
    }

    // Reactive statement: run getLastPaymentMonth whenever amountToPay changes
    $: endPointToPay = getLastPaymentMonth(amountToPay);
</script>

<div>
    {#each balances as balance, indexYear}
        <div class="flex items-center">
            <!-- <button>
                <iconify-icon
                    class="rotate-180 relative top-1"
                    icon="grommet-icons:form-next"
                    width="24"
                    height="24"
                ></iconify-icon>
            </button> -->
            <p class="text-lg font-bold">
                {balance.school_lapse.start.slice(0, 4)} - {balance.school_lapse.end.slice(
                    0,
                    4,
                )}
            </p>
            <!-- <button>
                <iconify-icon
                    class="relative top-1"
                    icon="grommet-icons:form-next"
                    width="24"
                    height="24"
                ></iconify-icon>
            </button> -->
        </div>
        <div class="grid p-0 grid-cols-12 border-2 border-black">
            <div class="col-span-1 bg-green font-bold flex justify-center pt-1">
                Inscr
            </div>
            <div class="col-span-11 grid grid-cols-12">
                {#each Object.entries(months) as [spanishLabel, month], indexMonth}
                    <div
                        class={` hover:brightness-110  relative col-span-1 z-10  text-sm capitalize overflow-hidden text-center font-bold ${balance[month + "_status"] == "debt" ? "bg-red" : balance[month + "_status"] == "paid" ? "bg-green" : balance[month + "_status"] == "partially_paid" ? "bg-yellow" : "bg-gray-100 border-l-2 border-l-gray-400"} text-black  p-2`}
                    >
                        {spanishLabel}
                        <p>
                            {#if balance[month + "_status"] == "debt" || balance[month + "_status"] == "partially_paid"}
                                ${Math.abs(balance[month])}
                            {/if}
                        </p>

                        <div
                            class={`months_to_pay absolute top-0.5 left-0 w-full h-[95%] z-40 
                            ${indexMonth === startPointToPay.month && startPointToPay.school_lapse_index == +indexYear ? "border-l-4 border-black/50" : ""} 
                            ${indexMonth === endPointToPay.endMonthIndex - 1 && endPointToPay.endYearIndex == +indexYear ? "border-r-4 border-black/50" : ""}
                            ${endPointToPay.endYearIndex == indexYear && +startPointToPay.month <= +indexMonth && endPointToPay.endMonthIndex > indexMonth ? "bg-purple/30 border-y-4 border-black/50" : ""}`}
                            style={indexMonth ===
                                endPointToPay.endMonthIndex - 1 &&
                            endPointToPay.endYearIndex == +indexYear &&
                            endPointToPay.partialToPay > 0
                                ? `width: ${(endPointToPay.partialToPay / Math.abs(balance[month])) * 100}%`
                                : ""}
                        ></div>
                    </div>
                {/each}
            </div>
        </div>
        <div class="flex gap-2">
            <p>Deuda Total:</p>
            <b>
                ${Math.abs(
                    Object.entries(months).reduce((total, [_, month]) => {
                        if (
                            (balance[month] < 0 &&
                                balance[month + "_status"] == "debt") ||
                            balance[month + "_status"] == "partially_paid"
                        ) {
                            total += balance[month];
                        }
                        return total;
                    }, 0),
                )}
            </b>
        </div>
    {/each}
</div>
