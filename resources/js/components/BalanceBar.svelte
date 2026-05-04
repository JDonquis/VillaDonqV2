<script>
    let balances = [
        {
            id: 49,
            student_id: 49,
            status: "pending",
            inscription: -50,
            inscription_status: "pending",
            january: -50,
            january_status: "pending",
            february: -50,
            february_status: "pending",
            march: -50,
            march_status: "pending",
            april: -50,
            april_status: "pending",
            may: -50,
            may_status: "pending",
            june: -50,
            june_status: "pending",
            july: -50,
            july_status: "pending",
            august: -50,
            august_status: "pending",
            september: -50,
            september_status: "debt",
            october: -50,
            october_status: "debt",
            november: -50,
            november_status: "debt",
            december: -50,
            december_status: "debt",
            school_lapse_id: 1,
            created_at: "2026-05-01T03:44:12.000000Z",
            updated_at: "2026-05-01T03:44:12.000000Z",
            school_lapse: {
                id: 1,
                start: "2026-09-01",
                end: "2027-08-31",
                status: 1,
                created_at: "2026-05-01 03:44:08",
                updated_at: "2026-05-01 03:44:08",
            },
        },
    ];
    $: console.log(balances);
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
            const status = balances[0]?.[`${monthName}_status`];
            return status === "debt" || status === "partially_paid";
        },
    );

    const startPointToPay = {
        school_lapse_index: 0,
        month: firstUnpaidMonth, // Si no hay deudas, cae al primer mes por defecto
    };

    let endPointToPay = {};
    console.log(firstUnpaidMonth);
    let balanceInscription = Math.abs(balances[0].inscription);
    function getLastPaymentMonth(amountToPay) {
        let lastPaymentMonth = null;
        let endMonthIndex = firstUnpaidMonth;
        let endYearIndex = startPointToPay.school_lapse_index;
        let partialToPay = 0;
        const arrMonthsEnglish = Object.values(months);
        if (balances[0].inscription < 0) {
            balanceInscription = amountToPay;
            amountToPay -= Math.abs(balances[0].inscription);
            console.log(amountToPay, balances[0].inscription);
        }
        while (amountToPay > 0) {
            const balance = Math.abs(
                balances[endYearIndex][arrMonthsEnglish[endMonthIndex]],
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
                startPointToPay,
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
    $: console.log(balances[0].inscription);
</script>

<div>
    {#each balances as balance, indexYear}
        <div class="flex gap-4 items-center">
            <!-- <button>
                <iconify-icon
                    class="rotate-180 relative top-1"
                    icon="grommet-icons:form-next"
                    width="24"
                    height="24"
                ></iconify-icon>
            </button> -->
            <p class="text-sm font-bold">
                {balance.school_lapse.start.slice(0, 4)} - {balance.school_lapse.end.slice(
                    0,
                    4,
                )}
            </p>

            <div class="flex gap-2">
                <p>Deuda:</p>
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
                    ) + Math.abs(balance.inscription)}
                </b>
            </div>
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
            <div
                class={` hover:brightness-110  relative col-span-1 z-10  text-sm capitalize overflow-hidden text-center font-bold ${balance.inscription < 0 ? "bg-red" : "bg-green"} text-black  p-2`}
            >
                <span> Inscri. </span>
                <p>${Math.abs(balance.inscription)}</p>

                <div
                    class={`absolute top-0.5 left-0  h-[95%] z-40 ${endPointToPay.endYearIndex == indexYear && balanceInscription > 0 ? "bg-purple/30 border-y-4 border-black/50 border-3" : ""}`}
                    style={endPointToPay.endYearIndex == +indexYear &&
                    balanceInscription > 0
                        ? `width: ${(balanceInscription / Math.abs(balance.inscription)) * 100}%`
                        : ""}
                ></div>
            </div>
            <div class="col-span-11 grid grid-cols-12">
                {#each Object.entries(months) as [spanishLabel, month], indexMonth}
                    <div
                        class={` hover:brightness-110 border-l-2 border-l-gray-200 relative col-span-1  text-sm capitalize overflow-hidden text-center font-bold ${balance[month + "_status"] == "debt" ? "bg-red" : balance[month + "_status"] == "paid" ? "bg-green" : balance[month + "_status"] == "partially_paid" ? "bg-yellow" : "bg-gray-50 "} text-black  p-2`}
                    >
                        <div class="z-50">
                            {spanishLabel}
                        </div>
                        <p>
                            {#if balance[month + "_status"] == "debt" || balance[month + "_status"] == "partially_paid"}
                                ${Math.abs(balance[month])}
                            {/if}
                        </p>

                        <div
                            class={`text-sm  months_to_pay absolute top-0.5 left-0 w-full text-black h-[95%] z-40 
                            ${indexMonth === startPointToPay.month && startPointToPay.school_lapse_index == +indexYear && amountToPay > Math.abs(balance[month]) ? "border-l-4 border-black/50" : ""} 
                            ${indexMonth === endPointToPay.endMonthIndex - 1 && endPointToPay.endYearIndex == +indexYear && amountToPay > 0 ? "border-r-4 border-black/50" : ""}
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
    {/each}
</div>
