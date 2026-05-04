<script>
    import Table from "../../components/Table.svelte";
    import Modal from "../../components/Modal.svelte";
    import Input from "../../components/Input.svelte";
    import Alert from "../../components/Alert.svelte";
    import { getMonitor } from "consulta-dolar-venezuela";
    import { displayAlert } from "../../stores/alertStore";
    import { useForm } from "@inertiajs/svelte";
    import axios from "axios";
    import debounce from "lodash/debounce";
    import ColorsPayMethods from "../../components/ColorsPayMethods";
    import BalanceBar from "../../components/BalanceBar.svelte";
    export let data = { students: { data: [] }, accounts: { data: [] } };

    // const balances = [
    //     {
    //         id: 58,
    //         student_id: 58,
    //         status: "pending",
    //         inscription: -50,
    //         january: 0,
    //         january_status: "paid",
    //         february: 0,
    //         february_status: "paid",
    //         march: -30,
    //         march_status: "partially_paid",
    //         april: -50,
    //         april_status: "debt",
    //         may: -50,
    //         may_status: "pending",
    //         june: -50,
    //         june_status: "pending",
    //         july: -50,
    //         july_status: "pending",
    //         august: -50,
    //         august_status: "pending",
    //         september: 0,
    //         september_status: "paid",
    //         october: 0,
    //         october_status: "paid",
    //         november: 0,
    //         november_status: "paid",
    //         december: 0,
    //         december_status: "paid",
    //         school_lapse_id: 1,
    //         created_at: "2026-04-27T19:59:49.000000Z",
    //         updated_at: "2026-04-27T19:59:49.000000Z",
    //         school_lapse: {
    //             id: 1,
    //             start: "2026-09-01",
    //             end: "2027-08-31",
    //             status: 1,
    //             created_at: "2026-04-27 19:59:43",
    //             updated_at: "2026-04-27 19:59:43",
    //         },
    //     },
    // ];

    export let searched_students = [];
    let isSearchTableOpen = false;
    let searchInputRef;
    let searchTableRef;
    const currentDate = new Date();
    let dolarPrice;

    getMonitor("BCV", "lastUpdate")
        .then((response) => {
            dolarPrice = response.bcv.price;
        })
        .catch((error) => {
            console.error("Error:", error);
        });

    // Format the date as a string in the "YYYY-MM-DD" format
    const currentDateString = currentDate.toISOString().split("T")[0];

    const emptyDataForm = {
        date: currentDateString,
        students: [],
        account_payment_id: "",
        total_in_dolars: "1",
        total_in_bs: "",
        reference: "",
        observations: "",
    };

    let form = useForm({
        date: currentDateString,
        students: [],
        account_payment_id: "",
        total_in_dolars: "1",
        total_in_bs: "",
        reference: "",
        observations: "",
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
        $form.clearErrors();
        $form.post("/dashboard/pagos", {
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
            onSuccess: (mensaje) => {
                $form.reset();
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

    const search_student = debounce(async (search_text) => {
        isSearchTableOpen = search_text.length > 0;
        try {
            const response = await axios.get(
                "/dashboard/pagos/search-student?",
                {
                    params: { search: search_text },
                },
            );
            searched_students = response.data;
            // Aquí puedes actualizar el estado con los resultados de la búsqueda
        } catch (error) {
            console.error("Error al buscar estudiantes:", error);
        }
    }, 300);

    // Ocultar tabla al hacer click fuera
    function handleClickOutside(event) {
        if (
            isSearchTableOpen &&
            !searchTableRef?.contains(event.target) &&
            !searchInputRef?.contains(event.target)
        ) {
            isSearchTableOpen = false;
            searched_students = [];
        }
    }

    // Agregar y remover el event listener
    import { onMount, onDestroy } from "svelte";
    onMount(() => {
        document.addEventListener("mousedown", handleClickOutside);
    });
    onDestroy(() => {
        document.removeEventListener("mousedown", handleClickOutside);
    });

    function handleDelete(id) {
        $form.delete(`/dashboard/matriculo/${id}`, {
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

    $: $form.total_in_dolars, exchange();

    function exchange() {
        // $form.total_in_bs = $form.total_in_dolars * +dolarPrice;
        // $form.total_in_dolars = $form.total_in_bs / dolarPrice;
    }
</script>

<svelte:head>
    <title>Pagos</title>
</svelte:head>

<Alert />

<Modal bind:showModal classes="w-11/12">
    <h2 slot="header" class="text-sm text-center">REGISTRO DE PAGO</h2>

    <form
        id="a-form"
        on:submit={handleSubmit}
        action=""
        class="w-full grid md:grid-cols-12 md:gap-x-5 px-5"
    >
        <div class="col-span-8 relative mx-auto text-left w-full">
            <!-- <Input
                type="text"
                required={true}
                label={"Nombre"}
                bind:value={$form.name}
                error={$form.errors?.name}
            /> -->
            <input
                type="search"
                placeholder="Buscar Estudiante"
                class={"z-50 mx-auto p-2 mt-6 md:w-60 nb-input  border rounded-md"}
                bind:this={searchInputRef}
                on:input={(e) => {
                    search_student(e.target.value);
                }}
            />

            <table
                id="students-search-table"
                bind:this={searchTableRef}
                class={`${isSearchTableOpen ? "block" : "hidden"} w-full absolute font-semibold bg-paper top-12 max-h-[370px] min-h-[300px] overflow-y-scroll z-50 border-4 [&_*]:px-4 [&_*]:py-2 [&_*]:text-left bg-background border-black text-sm  mt-5`}
            >
                <thead class="">
                    <tr>
                        <th>Estudiante</th>
                        <th>C.I</th>
                        <th>Grado/Año</th>
                        <th>Rep Legal</th>
                    </tr>
                </thead>
                <tbody>
                    {#each searched_students as student}
                        <tr
                            class={` hover:bg-black/10  [&_*]:px-4 [&_*]:py-2 cursor-pointer bg-white bg-opacity-10 border-gray-500`}
                            on:click={() => {
                                // Verificar si el estudiante ya está en el arreglo
                                if (
                                    !$form.students.some(
                                        (s) => s.id === student.id,
                                    )
                                ) {
                                    $form.students = [
                                        ...$form.students,
                                        {
                                            id: student.id,
                                            name: student.name,
                                            balances: student.balances || [],
                                            last_name: student.last_name,
                                            ci: student.ci,
                                            course_name: student.course.name,
                                            section_name: student.section.name,
                                            legal_rep_name:
                                                student.representative.user
                                                    .name +
                                                " " +
                                                student.representative.user
                                                    .last_name,
                                            balances: student.balances,
                                        },
                                    ];
                                }
                                isSearchTableOpen = false;
                                searched_students = [];
                            }}
                        >
                            <td>{student.name} {student.last_name}</td>
                            <td>{student.ci}</td>
                            <td
                                >{student.course.name} - {student.section
                                    .name}</td
                            >
                            <td
                                >{student.representative.user.name}
                                {student.representative.user.last_name}</td
                            >
                        </tr>
                    {/each}
                </tbody>
            </table>

            <table
                id="selected_student"
                class={`${$form.students.length > 0 ? "block" : "hidden"}  w-full font-semibold relative    text-sm overflow-hidden mt-5`}
            >
                <thead class="[&_*]:px-4 [&_*]:py-2 [&_*]:text-left">
                    <tr>
                        <th>Dólares ($)</th>
                        <th>Bolívares (Bs)</th>
                        <th>Estudiante</th>
                        <th>C.I</th>
                        <th>Grado/Año</th>
                        <th>Rep Legal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {#each $form.students as student, i}
                        <tr
                            class={` w-full [&_*]:px-4 [&_*]:py-2 text-sm cursor-pointer  border-gray-500`}
                        >
                            <td>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-24 border-3 p-1 border- small-shadow focus:outline-0"
                                    value={student.amount_in_dolars || ""}
                                    on:input={(e) => {
                                        $form.students[i] = {
                                            ...$form.students[i],
                                            amount_in_dolars: e.target.value,
                                            amount_in_bs: (
                                                e.target.value * dolarPrice
                                            ).toFixed(2),
                                        };
                                        $form.total_in_dolars = $form.students
                                            .reduce(
                                                (total, s) =>
                                                    total +
                                                    (parseFloat(
                                                        s.amount_in_dolars,
                                                    ) || 0),
                                                0,
                                            )
                                            .toFixed(2);
                                        $form.total_in_bs = (
                                            $form.total_in_dolars * dolarPrice
                                        ).toFixed(2);
                                    }}
                                />
                            </td>
                            <td class="w-36">
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-full border-3 p-0.5 border-black small-shadow focus:outline-0"
                                    value={student.amount_in_bs || ""}
                                    on:input={(e) => {
                                        $form.students[i] = {
                                            ...$form.students[i],
                                            amount_in_bs: e.target.value,
                                            amount_in_dolars: (
                                                e.target.value / dolarPrice
                                            ).toFixed(2),
                                        };
                                        $form.total_in_bs = $form.students
                                            .reduce(
                                                (total, s) =>
                                                    total +
                                                    (parseFloat(
                                                        s.amount_in_bs,
                                                    ) || 0),
                                                0,
                                            )
                                            .toFixed(2);
                                        $form.total_in_dolars = (
                                            $form.total_in_bs / dolarPrice
                                        ).toFixed(2);
                                    }}
                                />
                            </td>
                            <td class="font-bold"
                                >{student.name} {student.last_name}</td
                            >
                            <td>{student.ci}</td>
                            <td
                                >{student.course_name} - {student.section_name}</td
                            >
                            <td>{student.legal_rep_name}</td>
                            <td class="max-w-[60px]">
                                <button
                                    type="button"
                                    class="h-full hover:bg-paper"
                                    on:click={() => {
                                        // Eliminar el estudiante del arreglo
                                        $form.students = $form.students.filter(
                                            (s) => s.id !== student.id,
                                        );
                                    }}
                                >
                                    <iconify-icon icon="line-md:close"
                                    ></iconify-icon>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" class="px-3">
                                <BalanceBar
                                    balances={student.balances}
                                    amountToPay={student.amount_in_dolars}
                                />
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>

        <div class="col-span-4 w-full grid md:grid-cols-2 md:gap-x-5">
            <Input
                type="date"
                required={true}
                label={"Fecha del pago"}
                bind:value={$form.date}
                error={$form.errors?.date}
                max={currentDateString}
            />
            <Input
                type="select"
                label={"Método de pago"}
                bind:value={$form.account_payment_id}
                error={$form.errors?.account_payment_id}
                required={true}
            >
                {#each data.accounts.data as account}
                    <option
                        value={account.id}
                        class={`border-l-4 mix-blend-difference  }`}
                    >
                        {account.payment_method_name}
                        {#if account.bank}- {account.bank}{/if}
                        {#if account.cash_currency}- {account.cash_currency}{/if}
                        {#if account.username}- {account.username}{/if}
                    </option>
                {/each}
            </Input>
            <Input
                type="number"
                label={"Total en Dólares ($)"}
                required={true}
                readonly={true}
                bind:value={$form.total_in_dolars}
                error={$form.errors?.total_in_dolars}
            />
            <Input
                type="number"
                label={"Total en Bolívares (Bs)"}
                readonly={true}
                bind:value={$form.total_in_bs}
                error={$form.errors?.total_in_bs}
            />
            <Input
                type="number"
                label={"Referencia"}
                required={true}
                bind:value={$form.reference}
                error={$form.errors?.reference}
            />
            <Input
                type="textarea"
                label={"Observaciones"}
                bind:value={$form.observations}
                error={$form.errors?.observations}
            />
        </div>
        <button
            type="submit"
            class="btn btn-green col-span-2 mt-7 flex items-center justify-center gap-3"
            disabled={$form.processing}
        >
            {#if $form.processing}
                Cargando...
            {:else}
                <iconify-icon
                    icon="material-symbols:save-sharp"
                    width="24"
                    height="24"
                />
                <span> Guardar </span>
            {/if}
        </button>
    </form>
</Modal>

<div class=" items-center">
    <button
        class="btn inline-block"
        on:click={(e) => {
            e.preventDefault();
            showModal = true;
        }}>Registrar pago</button
    >
    <p class="mt-3">
        1$ = {#if dolarPrice}{dolarPrice}{:else}<iconify-icon
                icon="line-md:loading-loop"
                width="24"
                height="24"
            ></iconify-icon>{/if} Bs
    </p>
</div>

<Table
    {selectedRow}
    serverSideData={data?.payments}
    on:fillFormToEdit={fillFormToEdit}
    on:clickDeleteIcon={() => {
        handleDelete(selectedRow.id);
    }}
    pagination={true}
>
    <thead slot="thead" class="sticky top-0 z-50">
        <tr>
            <th>id</th>
            <th>Fecha</th>
            <th>Estudiante/s</th>
            <th>Total USD$</th>
            <th>Total Bs</th>
            <th>Método de pago</th>
            <th>Referencia</th>
            <!-- <th>Representante</th> -->
        </tr>
    </thead>

    <tbody slot="tbody">
        {#each data?.payments?.data as row, i}
            <tr
                on:click={(e) => {
                    const clickPos = { x: e.clientX, y: e.clientY };
                    if (row.id != selectedRow.id) {
                        selectedRow = {
                            status: true,
                            id: row.id,
                            title: row.title,
                            _clickPosition: clickPos,
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
                class={`py-2 cursor-pointer hover:bg-gray-100 ${selectedRow.id == row.id ? "bg-color2 hover:bg-opacity-10 bg-opacity-10 brightness-110" : ""}`}
            >
                <td>{row.id}</td>
                <td>{row.date}</td>
                <td class=" space-y-2">
                    {#each row?.students as student, j}
                        <div class="flex items-center gap-2">
                            <span>
                                <b class=""
                                    ><span class="text-gray-600">$</span
                                    >{student.pivot.amount_in_dolars}
                                </b>
                                {student.name}
                                {student.last_name}
                                <span class="text-gray-500">
                                    | C.I:
                                    {student.ci}
                                    | {student.course.name}-{student.section
                                        .name}
                                </span>
                            </span>
                        </div>
                    {/each}
                </td>
                <!-- <td
                    >{row.representative.user.name}
                    {row.representative.user.last_name}</td
                > -->
                <td>${row.total_in_dolars}</td>
                <td>{row.total_in_bs} Bs</td>
                <td class="">
                    <!-- <ColorsPayMethods
                        payment_method_id={row.account_payment.method.name}
                        accounts={data.accounts.data}
                    /> -->
                    <span
                        class={`h-5 text-${ColorsPayMethods()[row.account_payment.method.name]}  bg-${ColorsPayMethods()[row.account_payment.method.name]} w-5  left-0 top-0`}
                        >|</span
                    >
                    {row.account_payment.method.name}
                    {#if row.account_payment.bank}- {row.account_payment
                            .bank}{/if}
                    {#if row.account_payment.cash_currency}- {row
                            .account_payment.cash_currency}{/if}
                    {#if row.account_payment.username}- {row.account_payment
                            .username}{/if}
                </td>
                <td>{row.reference}</td>
            </tr>
        {/each}
    </tbody>
</Table>

<style>
    .grid-container > div:first-child .months_to_pay {
        border-left: 3px solid white;
    }

    /* Selecciona el último DIV que es hijo directo del contenedor del grid */
    .grid-container > div:last-child .months_to_pay {
        border-right: 3px solid white;
    }
</style>
