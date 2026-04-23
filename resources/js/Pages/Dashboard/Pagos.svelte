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
    export let data = [];
    $: console.log({ data });
    export let searched_students = [];
    let isSearchTableOpen = false;
    let searchInputRef;
    let searchTableRef;
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

    // Format the date as a string in the "YYYY-MM-DD" format
    const currentDateString = currentDate.toISOString().split("T")[0];
    console.log(data);
    const emptyDataForm = {
        date: "",

        account_payment_id: "",
        total_in_dolars: "",
        total_in_bs: "",
    };

    let form = useForm({
        date: currentDateString,
        students: [],
        account_payment_id: "",
        total_in_dolars: "1",
        total_in_bs: "",
        reference: "",
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
            console.log(response);
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

    $: console.log($form);
    $: console.log(data.course_sections?.data?.[`course_${$form.course_id}`]);

    $: $form.total_in_dolars, exchange();

    function exchange() {
        // $form.total_in_bs = $form.total_in_dolars * +dolarPrice;
        // $form.total_in_dolars = $form.total_in_bs / dolarPrice;
        console.log("tambien");
    }
</script>

<svelte:head>
    <title>Pagos</title>
</svelte:head>

<Alert />

<Modal bind:showModal classes="w-[980px]">
    <h2 slot="header" class="text-sm text-center">REGISTRO DE PAGO</h2>

    <form
        id="a-form"
        on:submit={handleSubmit}
        action=""
        class="w-full grid md:grid-cols-2 md:gap-x-5 px-5"
    >
        <div class="col-span-2 relative mx-auto text-center w-full">
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
                class={`${$form.students.length > 0 ? "block" : "hidden"}  w-full font-semibold relative [&_*]:px-4 [&_*]:py-2 [&_*]:text-left bg-background  text-sm overflow-hidden mt-5`}
            >
                <thead class="">
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
                            class={` w-full [&_*]:px-4 [&_*]:py-2 cursor-pointer  border-gray-500`}
                        >
                            <td>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-24 border-3 p-1 border- small-shadow focus:outline-0 "
                                    value={student.amount_in_dolars || ""}
                                    on:input={(e) => {
                                        $form.students[i] =
                                            {
                                                ...$form.students[i],
                                                amount_in_dolars: e.target.value,
                                                amount_in_bs: (
                                                    e.target.value * dolarPrice
                                                ).toFixed(2),
                                            };
                                            $form.total_in_dolars = $form.students.reduce((total, s) => total + (parseFloat(s.amount_in_dolars) || 0), 0).toFixed(2);
                                            $form.total_in_bs = ($form.total_in_dolars * dolarPrice).toFixed(2);
                                    }}
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-28 border-3 p-1 border-black small-shadow focus:outline-0 "
                                    value={student.amount_in_bs || ""}
                                    on:input={(e) => {
                                        $form.students[i] =
                                            {
                                                ...$form.students[i],
                                                amount_in_bs: e.target.value,
                                                amount_in_dolars: (
                                                    e.target.value / dolarPrice
                                                ).toFixed(2),
                                            };
                                            $form.total_in_bs = $form.students.reduce((total, s) => total + (parseFloat(s.amount_in_bs) || 0), 0).toFixed(2);
                                            $form.total_in_dolars = ($form.total_in_bs / dolarPrice).toFixed(2);
                                    }}
                                />
                            </td>
                            <td>{student.name} {student.last_name}</td>
                            <td>{student.ci}</td>
                            <td
                                >{student.course_name} - {student.section_name}</td
                            >
                            <td>{student.legal_rep_name}</td>
                            <td class="max-w-[60px]">
                                <button
                                    type="button"
                                    class="h-full hover:bg-paper "
                                    on:click={() => {
                                        console.log("eliminar", student.id);
                                        // Eliminar el estudiante del arreglo
                                        $form.students = $form.students.filter(
                                            (s) => s.id !== student.id,
                                        );
                                    }}
                                >
                                    <iconify-icon
                                        icon="line-md:close"
                                      
                                    ></iconify-icon>
                                </button>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
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
    on:fillFormToEdit={fillFormToEdit}
    on:clickDeleteIcon={() => {
        handleDelete(selectedRow.id);
    }}
    pagination={false}
>
    <thead slot="thead" class="sticky top-0 z-50">
        <tr>
            <th>id</th>
            <th>Fecha</th>
            <th>Estudiante</th>
            <th>Representante legal</th>
            <th>Monto USD$</th>
            <th>Monto Bs</th>
            <th>Método de pago</th>
            <th>Referencia</th>
            <!-- <th>Representante</th> -->
        </tr>
    </thead>

     <tbody slot="tbody">
         <!-- {#each data?.students?.data as row, i} 
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
                <td>{row.id}</td>
                <td>{row.date}</td>
                <td>{row.student_name} {row.student_last_name} {row.student_ci} </td>
                <td>{row.student_sex}</td>
                <td>{row.student_date_birth}</td>
                <td>{row.rep_name} {row.rep_last_name}</td>
                <td>{row.rep_phone_number}</td>
            </tr>
        {/each}  -->
    </tbody> 
</Table>
