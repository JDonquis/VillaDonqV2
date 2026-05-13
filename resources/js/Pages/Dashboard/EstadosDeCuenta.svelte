<script>
    import BalanceBar from "../../components/BalanceBar.svelte";
    import Search from "../../components/Search.svelte";
    import Table from "../../components/Table.svelte";
    import html2canvas from "html2canvas";

    export let data = [];
    console.log(data.students.data);
    console.table(data);

    async function sendToWhatsApp(student) {
        const element = document.getElementById(`balance-bar-${student.id}`);
        if (element) {
            const canvas = await html2canvas(element, {
                scale: 2,
                backgroundColor: "#ffffff",
                logging: false,
                useCORS: true
            });
            canvas.toBlob(async (blob) => {
                try {
                    const item = new ClipboardItem({ "image/png": blob });
                    await navigator.clipboard.write([item]);
                    alert(
                        "Imagen del balance copiada al portapapeles. ¡Pégala en el chat de WhatsApp!",
                    );
                } catch (err) {
                    console.error("Error al copiar al portapapeles:", err);
                    // Fallback: download the image if clipboard fails
                    const link = document.createElement("a");
                    link.download = `balance-${student.name}-${student.last_name}.png`;
                    link.href = canvas.toDataURL();
                    link.click();
                    alert(
                        "No se pudo copiar al portapapeles automáticamente. La imagen se ha descargado. ¡Adjúntala en WhatsApp!",
                    );
                }
            });
        }

        let phoneNumber = student.representative.user.phone_number.replace(
            /[ -]/g,
            "",
        );
        if (!phoneNumber || phoneNumber.length < 9) return;
        if (!phoneNumber.startsWith("+") && !phoneNumber.startsWith("58")) {
            phoneNumber = "58" + phoneNumber;
        }
        phoneNumber = phoneNumber.replace("+", "");

        const text = `Hola ${student.representative.user.name} ${student.representative.user.last_name}, Le escribimos para recordarle que el balance de su representado ${student.name} ${student.last_name} está vencido. Por favor, póngase al día con los pagos para evitar inconvenientes. Gracias!`;
        window.open(`https://wa.me/${phoneNumber}?text=${encodeURIComponent(text)}`, "_blank");
    }
</script>

<svelte:head>
    <title>Estados de Cuenta</title>
</svelte:head>

<Search placeholder="Buscar estudiante..." class="mb-4" />
<!-- svelte-ignore missing-declaration -->
<Table serverSideData={data?.students.data} pagination={true}>
    <thead slot="thead">
        <tr>
            <th>Estudiante</th>
            <th>Rep Legal</th>
            <th>Balance</th>
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
                <td>
                    <BalanceBar
                        id={`balance-bar-${student.id}`}
                        balances={student.balances.map((b) => ({
                            ...b,
                            ...b.months,
                        }))}
                    />
                </td>
            </tr>
        {/each}
    </tbody></Table
>
