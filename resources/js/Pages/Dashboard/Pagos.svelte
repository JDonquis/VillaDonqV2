<script>
    import Table from "../../components/Table.svelte";
    import Modal from "../../components/Modal.svelte";
    import Input from "../../components/Input.svelte";
    import Alert from "../../components/Alert.svelte";
    import { getMonitor } from "consulta-dolar-venezuela";
    import { displayAlert } from "../../stores/alertStore";
    import { useForm } from "@inertiajs/svelte";
    export let data = [];
    const currentDate = new Date();
    let dolarPrice;

    getMonitor("BCV", "lastUpdate")
        .then((response) => {
            dolarPrice = response.bcv.price;
            console.log(dolarPrice);
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    $: console.log(dolarPrice);

    // Format the date as a string in the "YYYY-MM-DD" format
    const currentDateString = currentDate.toISOString().split("T")[0];
    console.log(data);
    const emptyDataForm = {
        date: "",
        name: "",
        currency: "",
        payment_method: "",
        amount: "",
        change: "",
        vaucher: "",
        bs: "",
    };

    let formCreate = useForm({
        date: currentDateString,
        name: "Fabian",
        currency: "Bolivar",
        payment_method: "",
        amount: "1295",
        bs: "",
        vaucher: "1234568",
    });

    let formEdit = useForm({
        ...emptyDataForm,
    });

    let showModal = false;
    $: showModalFormEdit = false;
    let selectedRow = { status: false, id: 0 };

    document.addEventListener("keydown", ({ key }) => {
        if (key === "Escape") {
            selectedRow = { status: false, id: 0 };
        }
    });

    function handleSubmit(event) {
        event.preventDefault();
        $formCreate.clearErrors();
        $formCreate.post("/dashboard/matricula", {
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
            onSuccess: (mensaje) => {
                $formCreate.reset();
                displayAlert({
                    type: "success",
                    message: "Ok todo salió bien",
                });
                showModal = false;
            },
        });
    }
    function handleEdit(event) {
        event.preventDefault();
        $formEdit.clearErrors();
        $formEdit.put(`/dashboard/bitacora/${$formEdit.id}`, {
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
            onSuccess: (mensaje) => {
                $formEdit.reset();
                displayAlert({
                    type: "success",
                    message: "Ok todo salió bien",
                });
                showModalFormEdit = false;
                selectedRow = { status: false, id: 0, row: {} };
            },
        });
    }

    function handleDelete(id) {
        $formCreate.delete(`/dashboard/matriculo/${id}`, {
            onBefore: () =>
                confirm(
                    `¿Está seguro de eliminar a este estudiante ${selectedRow.title}?`,
                ),
        });
    }

    function fillFormToEdit() {
        $formEdit.reset();
        showModalFormEdit = true;
    }

    $: console.log($formCreate);
    $: console.log(
        data.course_sections?.data?.[`course_${$formCreate.course_id}`],
    );

    $: $formCreate.amout, exchange();

    function exchange() {
        // $formCreate.bs = $formCreate.amount * +dolarPrice;
        // $formCreate.amount = $formCreate.bs / dolarPrice;
        console.log('tambien')
    }
</script>

<svelte:head>
    <title>Pagos</title>
</svelte:head>

<Alert />

<Modal bind:showModal>
    <h2 slot="header" class="text-sm text-center">REGISTRO DE PAGO</h2>

    <form
        id="a-form"
        on:submit={handleSubmit}
        action=""
        class="w-fit grid md:grid-cols-2 md:gap-x-5"
    >
        <div class="col-span-2 mx-auto text-center w-full">
            <!-- <Input
                type="text"
                required={true}
                label={"Nombre"}
                bind:value={$formCreate.name}
                error={$formCreate.errors?.name}
            /> -->
            <input
                type="search"
                required={true}
                placeholder="Buscar Estudiante"
                class={"z-50 mx-auto p-2 mt-6 md:w-60 bg-color1 text-white border rounded-md"}
            /><iconify-icon
                class="relative top-2 ml-3 text-2xl"
                icon="material-symbols:search"
            ></iconify-icon>

            <table
                class="[&_*]:px-4 [&_*]:py-2 [&_*]:text-left text-sm rounded-md overflow-hidden mt-5"
            >
                <thead class="bg-color2">
                    <tr>
                        <th>Estudiante</th>
                        <th>Fecha</th>
                        <th>Metodo d </th>
                        <th>Sección</th>
                        <th>Rep Legal</th>
                        <th>Quitar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="[&_*]:px-4 [&_*]:py-2 cursor-pointer bg-white bg-opacity-10 border-b border-gray-500"
                    >
                        <td>Fabian Eduardo Vidal Molina</td>
                        <td>3434534</td>
                        <td>2do año</td>
                        <td>A</td>
                        <td>Maria de los Angeles</td>
                        <td
                            ><iconify-icon icon="mynaui:delete"
                            ></iconify-icon></td
                        >
                    </tr>
                </tbody>
            </table>
        </div>
        <Input
            type="date"
            required={true}
            label={"Fecha del pago"}
            bind:value={$formCreate.date}
            error={$formCreate.errors?.date}
            max={currentDateString}
        />
        <Input
            type="select"
            label={"Metodo de pago"}
            bind:value={$formCreate.payment_method}
            error={$formCreate.errors?.payment_method}
            required={true}
        >
            <option value="Masculino">Pago movil BNC</option>
            <option value="Femenino">Pago movil BBVA</option>
            <option value="Femenino">Tranferencia BNC</option>
            <option value="Femenino">Transferencia BBVA</option>
            <option value="Femenino">Zelle</option>
            <option value="Bolivares">Efectivo Bolivares</option>
            <option value="Dolares">Efectivo Dolares</option>
        </Input>
        <Input
            type="number"
            label={"Monto en Dolares ($)"}
            required={true}
            bind:value={$formCreate.amount}
            error={$formCreate.errors?.amount}
            on:input={(e) => {
                $formCreate.bs = (e.target.value * dolarPrice).toFixed(2)
            }}
        />
        <Input
            type="number"
            label={"Monto en Bolivares (Bs)"}
            bind:value={$formCreate.bs}
            error={$formCreate.errors?.bs}
            on:input={(e) => {
                $formCreate.amount = (e.target.value / dolarPrice).toFixed(2)
            }}
        />
        <Input
            type="number"
            label={"Referencia"}
            required={true}
            bind:value={$formCreate.vaucher}
            error={$formCreate.errors?.vaucher}
        />
    </form>
    <input
        form="a-form"
        slot="btn_footer"
        type="submit"
        value={$formCreate.processing ? "Cargando..." : "Guardar"}
        class="hover:bg-color3 hover:text-white duration-200 mt-auto w-full bg-color4 text-black font-bold py-3 rounded-md cursor-pointer"
    />
</Modal>

<!-- <Modal bind:showModal={showModalFormEdit}>
    <h2 slot="header" class="text-sm text-center">EDITAR PAGO</h2>

    <form id="a-form" on:submit={handleSubmit} action="" class="w-[600px]">
        <Input
            type="date"
            required={true}
            label={"Fecha"}
            bind:value={$formEdit.date}
            error={$formEdit.errors?.date}
        />
        <Input
            type="text"
            required={true}
            label={"Nombre"}
            bind:value={$formEdit.name}
            error={$formEdit.errors?.name}
        />
        <Input
            type="select"
            required={true}
            label={"Moneda"}
            bind:value={$formEdit.currency}
            error={$formEdit.errors?.currency}
        >
            <option value="Bolivares">Bolivares</option>
            <option value="Dolares">Dolares</option>
        </Input>
        <Input
            type="select"
            label={"Metodo de pago"}
            bind:value={$formEdit.payment_method}
            error={$formEdit.errors?.payment_method}
        >
            <option value="Masculino">Pago movil BNC</option>
            <option value="Femenino">Pago movil BBVA</option>
            <option value="Femenino">Tranferencia BNC</option>
            <option value="Femenino">Transferencia BBVA</option>
            <option value="Femenino">Zelle</option>
        </Input>
        <Input
            type="number"
            label={"Monto"}
            bind:value={$formEdit.amount}
            error={$formEdit.errors?.amount}
        />
        <Input
            type="number"
            label={"Cambio"}
            bind:value={$formEdit.change}
            error={$formEdit.errors?.change}
        />
        <Input
            type="number"
            label={"Comprobante"}
            bind:value={$formEdit.vaucher}
            error={$formEdit.errors?.vaucher}
        />
    </form>
    <input
        form="a-form"
        slot="btn_footer"
        type="submit"
        value={$formEdit.processing ? "Cargando..." : "Editar"}
        class="hover:bg-color3 hover:text-white duration-200 mt-auto w-full bg-color2 text-black font-bold py-3 rounded-md cursor-pointer"
    />
</Modal> -->

<div class="flex justify-between items-center">
    <button
        class="btn_create inline-block"
        on:click={(e) => {
            e.preventDefault();
            showModal = true;
        }}>Registrar pago</button
    >
</div>

<Table
    {selectedRow}
    on:fillFormToEdit={fillFormToEdit}
    on:clickDeleteIcon={() => {
        handleDelete(selectedRow.id);
    }}
    pagination={false}
>
    <thead slot="thead" class="sticky top-0 z-50">
        <tr>
            <th>Nro</th>
            <th>Fecha</th>
            <th>Sección</th>
            <!-- <th>Representante</th> -->
            <th>Nombre repre</th>
            <th>Metodo de pago</th>
            <th>Ref</th>
            <th>Monto ($)</th>
            <th>Monto (bs)</th>
            <th>Estado</th>
        </tr>
    </thead>

    <!-- <tbody slot="tbody">
        {#each data.students.data as row, i}
            <tr
                on:click={(e) => {
                    // let newSelectedRowStatus = !selectedRow.status;
                    if (row.id != selectedRow.id) {
                        selectedRow = {
                            status: true,
                            id: row.id,
                            title: row.title,
                        };
                        $formEdit.defaults({
                            ...row,
                        });
                    } else {
                        selectedRow = {
                            status: false,
                            id: 0,
                            title: "",
                        };
                        $formEdit.defaults({
                            ...emptyDataForm,
                        });
                    }
                }}
                class={`cursor-pointer hover:bg-gray-500 hover:bg-opacity-5 ${selectedRow.id == row.id ? "bg-color2 hover:bg-opacity-10 bg-opacity-10 brightness-110" : ""}`}
            >
                <td>{i + 1}</td>
                <td>{row.student_name}</td>
                <td>{row.student_last_name}</td>
                <td>{row.student_ci}</td>
                <td>{row.student_sex}</td>
                <td>{row.student_date_birth}</td>
                <td>{row.rep_name} {row.rep_last_name}</td>
                <td>{row.rep_phone_number}</td>
            </tr>
        {/each}
    </tbody> -->
</Table>
