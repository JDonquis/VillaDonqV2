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
    import Search from "../../components/Search.svelte";
    import SelectableRow from "../../components/SelectableRow.svelte";
    import { onMount, onDestroy } from "svelte";

    export let data = { students: { data: [] }, accounts: { data: [] } };

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
    let showTotalIncome = false;
    $: showModalFormEdit = false;
    let selectedRow = { status: false, data: null };
    let submitStatus = "Registrar";

    document.addEventListener("keydown", ({ key }) => {
        if (key === "Escape") {
            selectedRow = { status: false, data: null };
        }
    });
    console.log(data);
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
    onMount(() => {
        document.addEventListener("mousedown", handleClickOutside);
    });
    onDestroy(() => {
        document.removeEventListener("mousedown", handleClickOutside);
    });

    function handleDelete(id) {
        if (selectedRow.data?.status == 0) {
            displayAlert({ type: "error", message: "Este pago ya ha sido eliminado" });
            return;
        }
        $form.delete(`/dashboard/pagos/${id}`, {
            onBefore: () => confirm(`¿Está seguro de eliminar este pago?`),
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
            onSuccess: (mensaje) => {
                displayAlert({
                    type: "success",
                    message: "Pago eliminado correctamente",
                });
                 selectedRow = { status: false, data: null };
            }
        });
    }

    async function fillFormToEdit() {
        showModal = true;
        submitStatus = "Editar";
        const selectedData = selectedRow.data;
        console.log({ selectedData });

        const studentsWithBalances = await Promise.all(
            selectedData.students.map(async (s) => {
                const response_student = await getBalanceByStudentId(s.id);
                const studentData = Array.isArray(response_student)
                    ? response_student[0]
                    : response_student;

                return {
                    ...s,
                    balances:
                        studentData?.balances?.length > 0
                            ? studentData.balances
                            : s.balances || [],
                };
            }),
        );

        $form.id = selectedData.id;
        console.log({ studentsWithBalances });
        $form.students = studentsWithBalances.map((s) => ({
            id: s.id,
            name: s.name,
            last_name: s.last_name,
            ci: s.ci,
            course_name: s.course?.name || "",
            section_name: s.section?.name || "",
            legal_rep_name:
                s.representative?.user?.name +
                " " +
                s.representative?.user?.last_name,
            balances: s.balances || [],
            amount_in_dolars: s.pivot?.amount_in_dolars,
            amount_in_bs: s.pivot?.amount_in_bs,
        }));
        $form.date =  new Date(selectedData.date).toISOString().split("T")[0];
        $form.account_payment_id = selectedData.account_payment_id;
        $form.total_in_dolars = selectedData.total_in_dolars;
        $form.reference = selectedData.reference;
        $form.observations = selectedData.observations;
    }

    $: $form.total_in_dolars, exchange();

    function exchange() {
        // $form.total_in_bs = $form.total_in_dolars * +dolarPrice;
        // $form.total_in_dolars = $form.total_in_bs / dolarPrice;
    }

    const getBalanceByStudentId = async(studentId) => {
         try {
            const response = await axios.get(
                `/dashboard/pagos/search-student`, {
                    params: { id: studentId },
                }
            );
            console.log(response.data)
            return response.data;
        } catch (error) {
            console.log(error)
            return [];
        }
    };

    $: console.log($form);
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
        class="w-full grid md:grid-cols-12 md:gap-x-5 px-3 pl-2"
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
                class={"z-50 mx-auto p-2 mt-6 md:w-60 nb-input ml-5  border rounded-md"}
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
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {#each $form.students as student, i}
                        <tr
                            class={` w-full [&_td]:px-2 [&_td*]:py-2 text-sm cursor-pointer  border-gray-500`}
                        >
                            <td>
                                <div class="flex items-center">
                                    <b class="pr-1">$</b>
                                    <input
                                        type="number"
                                        min="0"
                                        placeholder="Dólares"
                                        step="0.01"
                                        class="w-28 border-3 py-2 px-3 border- small-shadow focus:outline-0"
                                        value={student.amount_in_dolars || ""}
                                        on:input={(e) => {
                                            $form.students[i] = {
                                                ...$form.students[i],
                                                amount_in_dolars:
                                                    e.target.value,
                                                amount_in_bs: (
                                                    e.target.value * dolarPrice
                                                ).toFixed(2),
                                            };
                                            $form.total_in_dolars =
                                                $form.students
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
                                                $form.total_in_dolars *
                                                dolarPrice
                                            ).toFixed(2);
                                        }}
                                    />
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <b class="pr-1 text-xs">VES</b>
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class=" w-32 border-3 py-2 px-3 border-3 border-black small-shadow focus:outline-0"
                                        value={student.amount_in_bs || ""}
                                        placeholder="Bolívares"
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
                                </div>
                            </td>
                            <td class="font-bold">
                                <div class="flex items-center">
                                    <iconify-icon
                                        icon="bx:child"
                                        width="14"
                                        height="14"
                                    ></iconify-icon>
                                    <span>
                                        {student.name}
                                        {student.last_name}
                                    </span>
                                </div>
                            </td>
                            <td>C.I:{student.ci}</td>
                            <td>
                                {student.course_name} - {student.section_name}
                            </td>
                            <td
                                ><div class="flex items-center">
                                    <span
                                        ><iconify-icon
                                            icon="bi:person-standing"
                                            width="16"
                                            height="16"
                                        ></iconify-icon>

                                        <span>{student.legal_rep_name}</span>
                                    </span>
                                </div>
                            </td>
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
                        <tr class=" ">
                            <td colspan="7" class="px-3 pb-10">
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
        <div class="flex justify-end col-span-12">
            <button
                type="submit"
                class="w-[420px] btn btn-green mt-7 flex items-center justify-center gap-3"
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
                    <span>
                        {submitStatus}
                    </span>
                {/if}
            </button>
        </div>
    </form>
</Modal>

<div class="flex flex-col justify-end items-end gap-3 mt-1">
    <button
        class="btn inline-block"
        on:click={(e) => {
            e.preventDefault();
            showModal = true;
        }}
    >
        Registrar pago
    </button>
    <p>
        1$ = {#if dolarPrice}{dolarPrice}{:else}<iconify-icon
                icon="line-md:loading-loop"
                width="24"
                height="24"
            ></iconify-icon>{/if} Bs
    </p>
</div>

<Search
    filtersOptions={{
        date: {
            type: "date",
            label: "Fecha de ingreso",
        },
        account_payment_id: {
            type: "select",
            multiple: true,
            label: "Método de pago",
            options: data.accounts.data.map((account) => ({
                id: account.id,
                name: [
                    account.payment_method_name,
                    account?.bank || "",
                    account?.cash_currency || "",
                    account?.username || "",
                ]
                    .filter(Boolean)
                    .join(" "),
                color: ColorsPayMethods()[account.payment_method_name],
            })),
        },
    }}
/>

{#if data.total_income}
    <div class="w-max mb-5 flex flex-wrap items-center gap-2">
        <span class="font-semibold">Total ingresos:</span>
        <b
            class={`text-sm ${showTotalIncome ? "opacity-100" : "opacity-0 blur-sm"} text-green transition-all duration-200`}
        >
            {showTotalIncome ? `$${data.total_income}` : "•••"}
        </b>
        <button
            type="button"
            class="inline-flex items-center justify-center bg-white/10 p-2 text-gray-700 transition hover:bg-green/10 focus:outline-none"
            on:click={() => {
                showTotalIncome = !showTotalIncome;
            }}
            aria-label={showTotalIncome ? "Ocultar total" : "Mostrar total"}
        >
            <iconify-icon
                icon={showTotalIncome ? "formkit:eyeclosed" : "mdi:eye-outline"}
                width="24"
                height="24"
            ></iconify-icon>
        </button>
    </div>
{/if}
<Table
    {selectedRow}
    serverSideData={data?.payments}
    on:clickDeleteIcon={() => {
        handleDelete(selectedRow.data?.id);
    }}
    edit={false}
    pagination={true}
>
    <thead slot="thead" class="sticky top-0 z-50">
        <tr>
            <th>id</th>
            <th>Fecha del pago</th>
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
            <SelectableRow
                rowData={row}
                idKey="id"
                {selectedRow}
                activeClass="bg-color2 bg-opacity-10 brightness-110"
                on:select={(e) => {
                    selectedRow = e.detail;
                    $formEdit.defaults(
                        e.detail.data ? { ...row } : { ...emptyDataForm },
                    );
                }}
                classes={`${row.status === 0 ? "bg-red text-gray-400 bg-opacity-10 opacity-70" : ""} `}
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
            </SelectableRow>
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
