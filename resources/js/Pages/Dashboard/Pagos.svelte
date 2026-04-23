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
    export let data = [];
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
        name: "",
        currency: "",
        payment_method: "",
        amount: "",
        change: "",
        vaucher: "",
        bs: "",
    };

    let form = useForm({
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
        $form.clearErrors();
        $form.post("/dashboard/matricula", {
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
                "/dashboard/pagos/search-student",
                {
                    params: { search: search_text },
                },
            );
            searched_students = response.data.students;
            console.log(response.data);
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

    $: $form.amout, exchange();

    function exchange() {
        // $form.bs = $form.amount * +dolarPrice;
        // $form.amount = $form.bs / dolarPrice;
        console.log("tambien");
    }
</script>

<svelte:head>
    <title>Pagos</title>
</svelte:head>

<Alert />

<Modal bind:showModal classes="w-[780px]">
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
                                $form.student_id = student.id;
                                $form.student_name = student.name;
                                $form.student_ci = student.cedula;
                                $form.student_grade = student.grade;
                                $form.student_legal_rep = student.legal_rep;
                                // Aquí puedes asignar otros campos del formulario según el estudiante seleccionado
                                isSearchTableOpen = false;
                                searched_students = [];
                            }}
                        >
                            <td>{student.name}</td>
                            <td>{student.cedula}</td>
                            <td>{student.grade}</td>
                            <td>{student.legal_rep}</td>
                        </tr>
                    {/each}
                </tbody>
            </table>

            <table
                id="selected_student"
                class={`${$form.student_id ? "block" : "hidden"} w-full font-semibold relative [&_*]:px-4 [&_*]:py-2 [&_*]:text-left bg-purple/30 border-purple text-sm overflow-hidden mt-5`}
            >
                <button
                    type="button"
                    class="absolute -top-1 -right-5 p-3 font-bold hover:bg-purple hover:border-2 border-black"
                >
                    <iconify-icon icon="line-md:close" width="10" height="10"
                    ></iconify-icon>
                </button>

                <thead class="">
                    <tr>
                        <th>Estudiante</th>
                        <th>C.I</th>
                        <th>Grado/Año</th>
                        <th>Rep Legal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="font-semibold [&_*]:px-4 [&_*]:py-2 cursor-pointer bg-white bg-opacity-10 border-gray-500"
                    >
                        <td>{$form.student_name}</td>
                        <td>{$form.student_ci}</td>
                        <td>{$form.student_grade}</td>
                        <td>{$form.student_legal_rep}</td>
                    </tr>
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
            label={"Metodo de pago"}
            bind:value={$form.payment_method}
            error={$form.errors?.payment_method}
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
            bind:value={$form.amount}
            error={$form.errors?.amount}
            on:input={(e) => {
                $form.bs = (e.target.value * dolarPrice).toFixed(2);
            }}
        />
        <Input
            type="number"
            label={"Monto en Bolivares (Bs)"}
            bind:value={$form.bs}
            error={$form.errors?.bs}
            on:input={(e) => {
                $form.amount = (e.target.value / dolarPrice).toFixed(2);
            }}
        />
        <Input
            type="number"
            label={"Referencia"}
            required={true}
            bind:value={$form.vaucher}
            error={$form.errors?.vaucher}
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

<div class=" items-center">
    <button
        class="btn inline-block"
        on:click={(e) => {
            e.preventDefault();
            showModal = true;
        }}>Registrar pago</button
    >
    <p class="mt-3">1$ = {dolarPrice} Bs</p>
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
            <th>Estudiante</th>
            <th>Representante legal</th>
            <th>Monto USD$</th>
            <th>Monto Bs</th>
            <th>Metodo de pago</th>
            <th>Referencia</th>
            <!-- <th>Representante</th> -->
   
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
