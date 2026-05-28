<script>
    import BalanceBar from "../../components/BalanceBar.svelte";
    import Search from "../../components/Search.svelte";
    import Table from "../../components/Table.svelte";
    import html2canvas from "html2canvas";
    import { page } from "@inertiajs/svelte";

    export let data = [];
    let showTotalDebt = false;
    $: tableData = {
        ...data?.students.data,
        filters: {
            debt_filter:
                new URLSearchParams($page.url.split("?")[1] || "").get(
                    "debt_filter",
                ) || "",
        },
    };

    async function sendToWhatsApp(student) {
        const element = document.getElementById(`balance-bar-${student.id}`);

        if (!element) return;

        try {
            const canvas = await html2canvas(element, {
                scale: 2,
                backgroundColor: "#ffffff",
                logging: false,
                useCORS: true,
            });

            // Convert canvas to blob
            const blob = await new Promise((resolve) =>
                canvas.toBlob(resolve, "image/png"),
            );

            // Copy image FIRST
            const item = new ClipboardItem({ "image/png": blob });
            await navigator.clipboard.write([item]);

            let phoneNumber = student.representative.user.phone_number.replace(
                /[ -]/g,
                "",
            );

            if (!phoneNumber || phoneNumber.length < 9) return;

            if (!phoneNumber.startsWith("+") && !phoneNumber.startsWith("58")) {
                phoneNumber = "58" + phoneNumber;
            }

            phoneNumber = phoneNumber.replace("+", "");

            const text = `Hola ${student.representative.user.name} ${student.representative.user.last_name}, esperamos que se encuentre muy bien.

Le contactamos para informarle que el pago mensual de ${student.name} ${student.last_name} se encuentra vencido. Le agradeceríamos ponerse al día cuando le sea posible para mantener su cuenta al día y evitar inconvenientes.

Gracias por su atención y apoyo continuo.`;

            // OPEN WHATSAPP AFTER clipboard succeeds
            window.open(
                `https://wa.me/${phoneNumber}?text=${encodeURIComponent(text)}`,
                "_blank",
            );

            // alert(
            //     "Imagen copiada al portapapeles. Solo pega la imagen en WhatsApp.",
            // );
            displayAlert({
                type: "info",
                message:
                    "Imagen copiada al portapapeles. Solo pega la imagen en WhatsApp.",
            });
        } catch (err) {
            console.error("Error al copiar al portapapeles:", err);

            // Fallback download
            const canvas = await html2canvas(element);

            const link = document.createElement("a");
            link.download = `balance-${student.name}-${student.last_name}.png`;
            link.href = canvas.toDataURL();
            link.click();

            // displayInfoAlert(
            //     "No se pudo copiar al portapapeles. Se ha descargado la imagen, por favor envíala manualmente por WhatsApp.",
            // );
            displayAlert({
                type: "info",
                message:
                    "No se pudo copiar al portapapeles. Se ha descargado la imagen, por favor envíala manualmente por WhatsApp.",
            });


        }
    }
</script>

<svelte:head>
    <title>Estados de Cuenta</title>
</svelte:head>

<Search placeholder="Buscar estudiante..." class="mb-4" />

{#if data.total_debt}
    <div class="w-max mb-5 flex flex-wrap items-center gap-2">
        <span class="font-semibold">Deuda:</span>
        <b
            class={`text-sm ${showTotalDebt ? "opacity-100" : "opacity-0 blur-sm"} text-red transition-all duration-200`}
        >
            {showTotalDebt ? `$${data.total_income}` : "•••"}
        </b>
        <button
            type="button"
            class="inline-flex items-center justify-center bg-white/10 p-2 text-gray-700 transition hover:bg-red/10 focus:outline-none"
            on:click={() => {
                showTotalDebt = !showTotalDebt;
            }}
            aria-label={showTotalDebt ? "Ocultar total" : "Mostrar total"}
        >
            <iconify-icon
                icon={showTotalDebt ? "formkit:eyeclosed" : "mdi:eye-outline"}
                width="24"
                height="24"
            ></iconify-icon>
        </button>
    </div>
{/if}
<!-- svelte-ignore missing-declaration -->
<Table
    serverSideData={tableData}
    pagination={true}
    filtersOptions={{
        debt_filter: [
            { id: "", name: "Todos" },
            { id: "debtors", name: "Deudores" },
            { id: "current_period", name: "Deudores del periodo actual" },
            { id: "previous_period", name: "Deudores del periodo anterior" },
            { id: "exempted", name: "Solo exonerados" },
            { id: "up_to_date", name: "Al día" },
        ],
    }}
>
    <thead slot="thead">
        <tr>
            <th>Estudiante</th>
            <th>Balance</th>
            <th>Rep Legal</th>
        </tr>
    </thead>
    <tbody slot="tbody">
        {#each data?.students.data as student}
            <tr>
                <td class=" space-y-2">
                    <div class="flex items-center gap-2">
                        <span>
                            {student.name}
                            {student.last_name}
                            <span class="text-gray-500">
                                | C.I:
                                {student.ci}
                                | {student.course.name}-{student.section.name}
                            </span>
                        </span>
                    </div>
                </td>
                <td>
                    <BalanceBar
                        id={`balance-bar-${student.id}`}
                        balances={student.balances.map((b) => ({
                            ...b,
                            ...b.months,
                        }))}
                        classes="py-0 px-0"
                        is_exempt={student.is_exempt
                            ? student.exemption_percentage
                            : false}
                    />
                </td>
                <td class="group"
                    >{student.representative.user.name}
                    {student.representative.user.last_name}

                    <button
                        title="Enviar por WhatsApp"
                        on:click={() => sendToWhatsApp(student)}
                        class="text-green cursor-pointer p-1 hover:bg-gray-100 hidden group-hover:inline-flex"
                    >
                        <iconify-icon
                            icon="ic:baseline-whatsapp"
                            width="14"
                            height="14"
                        ></iconify-icon>
                    </button>
                </td>
            </tr>
        {/each}
    </tbody></Table
>
