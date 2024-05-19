<script>
    import Table from "../../components/Table.svelte";
    import Modal from "../../components/Modal.svelte";
    import Input from "../../components/Input.svelte";
    import Alert from "../../components/Alert.svelte";

    import { displayAlert } from "../../stores/alertStore";
    import { useForm } from "@inertiajs/svelte";

    let formCreate = useForm({
        student_name: "",
        student_last_name: "",
        student_date_birth: "",
        student_email: "",
        student_CI: "",
        student_phone_number: "",
        course_id: "",
        section_id: "",
        student_sex: "",
        student_previous_school: "",
        state: "",
        city: "",
        address: "",
        rep_name: "",
        rep_last_name: "",
        rep_DNI: "",
        rep_phone_number: "",
        rep_email: "",
        rep_profession: "",
        rep_workplace: "",
        second_rep_name: "",
        second_rep_last_name: "",
        second_rep_DNI: "",
        second_rep_phone_number: "",
        second_rep_email: "",
        second_rep_profession: "",
        second_rep_workplace: "",
    });

    let formEdit = useForm({
        student_name: "",
        student_last_name: "",
        student_date_birth: "",
        student_email: "",
        student_DNI: "",
        student_phone_number: "",
        course_id: "",
        section_id: "",
        student_sex: "",
        student_previous_school: "",
        state: "",
        city: "",
        address: "",
        rep_name: "",
        rep_last_name: "",
        rep_DNI: "",
        rep_phone_number: "",
        rep_email: "",
        rep_profession: "",
        rep_workplace: "",
        second_rep_name: "",
        second_rep_last_name: "",
        second_rep_DNI: "",
        second_rep_phone_number: "",
        second_rep_email: "",
        second_rep_profession: "",
        second_rep_workplace: "",
    });

    let showModal = false;
    $: showModalFormEdit = false;
    $: selectedRow = { status: false, id: 0 };

    function handleSubmit(event) {
        event.preventDefault();
        $formCreate.clearErrors();
        $formCreate.post("/dashboard/bitacora", {
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
        $formCreate.delete(`/dashboard/bitacora/${id}`, {
            onBefore: () =>
                confirm(
                    `¿Está seguro de eliminar la actividad ${selectedRow.title}?`,
                ),
        });
    }
    export let data = [];

    function fillFormToEdit() {
        $formEdit.reset();
        showModalFormEdit = true;
    }

</script>

<svelte:head>
    <title>Matricula</title>
</svelte:head>

<Alert />

<Modal bind:showModal>
    <h2 slot="header" class="text-sm text-center">
        INSCRIBIR NUEVO ESTUDIANTE
    </h2>

    <form id="a-form" on:submit={handleSubmit} action="" class="w-[600px]">
        <fieldset
            class="px-5 snap-start bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >DATOS DEL ESTUDIANTE</legend
            >
            <Input
                type="text"
                required={true}
                label={"Nombre"}
                bind:value={$formCreate.student_name}
                error={$formCreate.errors?.student_name}
            />
            <Input
                type="text"
                required={true}
                label={"Apellido"}
                bind:value={$formCreate.student_last_name}
                error={$formCreate.errors?.studen_last_name}
            />
            <Input
                type="date"
                required={true}
                label={"Fecha de nacimiento"}
                bind:value={$formCreate.student_date_birth}
                error={$formCreate.errors?.student_date_birth}
            />
            <Input
                type="email"
                required={true}
                label="correo"
                bind:value={$formCreate.student_email}
                error={$formCreate.errors?.student_email}
            />
            <Input
                type="number"
                required={true}
                label={"Cédula"}
                bind:value={$formCreate.student_CI}
                error={$formCreate.errors?.student_CI}
            />
            <Input
                type="tel"
                required={true}
                label={"Teléfono"}
                bind:value={$formCreate.student_phone_number}
                error={$formCreate.errors?.student_phone_number}
            />
            <Input
                type="select"
                required={true}
                label={"Sexo"}
                bind:value={$formCreate.student_sex}
                error={$formCreate.errors?.student_sex}
            >
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
            </Input>
            <Input
                type="select"
                required={true}
                label={"Año escolar"}
                bind:value={$formCreate.course_id}
                error={$formCreate.errors?.course_id}
            >
                <option value="Masculino">1er año</option>
                <option value="Femenino">2do año</option>
            </Input>
            <Input
                type="select"
                required={true}
                label={"Sección"}
                bind:value={$formCreate.section_id}
                error={$formCreate.errors?.section_id}
            >
                <option value="Masculino">A</option>
                <option value="Femenino">B</option>
            </Input>

            <Input
                type="textarea"
                required={true}
                label={"Colegio de procedencia"}
                bind:value={$formCreate.student_previous_school}
                error={$formCreate.errors?.student_previous_school}
            />
        </fieldset>

        <fieldset
            class="px-5 snap-start bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >REPRESENTANTE LEGAL</legend
            >
            <Input
                type="text"
                required={true}
                label={"Nombre"}
                bind:value={$formCreate.student_name}
                error={$formCreate.errors?.student_name}
            />
            <Input
                type="text"
                required={true}
                label={"Apellido"}
                bind:value={$formCreate.student_last_name}
                error={$formCreate.errors?.studen_last_name}
            />
            <Input
                type="date"
                required={true}
                label={"Fecha de nacimiento"}
                bind:value={$formCreate.student_date_birth}
                error={$formCreate.errors?.student_date_birth}
            />
            <Input
                type="email"
                required={true}
                label="correo"
                bind:value={$formCreate.student_email}
                error={$formCreate.errors?.student_email}
            />
            <Input
                type="number"
                required={true}
                label={"Cédula"}
                bind:value={$formCreate.student_CI}
                error={$formCreate.errors?.student_CI}
            />
            <Input
                type="tel"
                required={true}
                label={"Teléfono"}
                bind:value={$formCreate.student_phone_number}
                error={$formCreate.errors?.student_phone_number}
            />
            <Input
                type="select"
                required={true}
                label={"Sexo"}
                bind:value={$formCreate.student_sex}
                error={$formCreate.errors?.student_sex}
            >
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
            </Input>
            <Input
                type="select"
                required={true}
                label={"Año escolar"}
                bind:value={$formCreate.course_id}
                error={$formCreate.errors?.course_id}
            >
                <option value="Masculino">1er año</option>
                <option value="Femenino">2do año</option>
            </Input>
            <Input
                type="select"
                required={true}
                label={"Sección"}
                bind:value={$formCreate.section_id}
                error={$formCreate.errors?.section_id}
            >
                <option value="Masculino">A</option>
                <option value="Femenino">B</option>
            </Input>

            <Input
                type="textarea"
                required={true}
                label={"Colegio de procedencia"}
                bind:value={$formCreate.student_previous_school}
                error={$formCreate.errors?.student_previous_school}
            />
        </fieldset>

        <fieldset
            class="px-5 snap-start bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >2DO REPRESENTANTE</legend
            >
            <Input
                type="text"
                required={true}
                label={"Nombre"}
                bind:value={$formCreate.student_name}
                error={$formCreate.errors?.student_name}
            />
            <Input
                type="text"
                required={true}
                label={"Apellido"}
                bind:value={$formCreate.student_last_name}
                error={$formCreate.errors?.studen_last_name}
            />
            <Input
                type="date"
                required={true}
                label={"Fecha de nacimiento"}
                bind:value={$formCreate.student_date_birth}
                error={$formCreate.errors?.student_date_birth}
            />
            <Input
                type="email"
                required={true}
                label="correo"
                bind:value={$formCreate.student_email}
                error={$formCreate.errors?.student_email}
            />
            <Input
                type="number"
                required={true}
                label={"Cédula"}
                bind:value={$formCreate.student_CI}
                error={$formCreate.errors?.student_CI}
            />
            <Input
                type="tel"
                required={true}
                label={"Teléfono"}
                bind:value={$formCreate.student_phone_number}
                error={$formCreate.errors?.student_phone_number}
            />
            <Input
                type="select"
                required={true}
                label={"Sexo"}
                bind:value={$formCreate.student_sex}
                error={$formCreate.errors?.student_sex}
            >
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
            </Input>
            <Input
                type="select"
                required={true}
                label={"Año escolar"}
                bind:value={$formCreate.course_id}
                error={$formCreate.errors?.course_id}
            >
                <option value="Masculino">1er año</option>
                <option value="Femenino">2do año</option>
            </Input>
            <Input
                type="select"
                required={true}
                label={"Sección"}
                bind:value={$formCreate.section_id}
                error={$formCreate.errors?.section_id}
            >
                <option value="Masculino">A</option>
                <option value="Femenino">B</option>
            </Input>

            <Input
                type="textarea"
                required={true}
                label={"Colegio de procedencia"}
                bind:value={$formCreate.student_previous_school}
                error={$formCreate.errors?.student_previous_school}
            />
        </fieldset>
    </form>
    <input
        form="a-form"
        slot="btn_footer"
        type="submit"
        value={$formCreate.processing ? "Cargando..." : "Guardar"}
        class="hover:bg-color3 hover:text-white duration-200 mt-auto w-full bg-color4 text-black font-bold py-3 rounded-md cursor-pointer"
    />
</Modal>

{#if showModalFormEdit}
    <Modal bind:showModal={showModalFormEdit}>
        <h2 slot="header" class="text-sm text-center">EDITAR ACTIVIDAD</h2>

        <form
            id="a-form"
            on:submit={handleEdit}
            action=""
            class="w-[500px] grid grid-cols-2 gap-x-5"
        >
            <Carousel perPage={1}>
                <fieldset class=" grid grid-cols-2 gap-x-5 w-full">
                    <legend>DATOS DEL ESTUDIANTE</legend>
                    <Input
                        type="text"
                        required={true}
                        label={"Nombre"}
                        bind:value={$formEdit.student_name}
                        error={$formEdit.errors?.student_name}
                    />
                    <Input
                        type="text"
                        required={true}
                        label={"Apellido"}
                        bind:value={$formEdit.student_last_name}
                        error={$formEdit.errors?.studen_last_name}
                    />
                    <Input
                        type="date"
                        required={true}
                        label={"Fecha de nacimiento"}
                        bind:value={$formEdit.student_date_birth}
                        error={$formEdit.errors?.student_date_birth}
                    />
                    <Input
                        type="email"
                        required={true}
                        label="correo"
                        bind:value={$formEdit.student_email}
                        error={$formEdit.errors?.student_email}
                    />
                    <Input
                        type="number"
                        required={true}
                        label={"Cédula"}
                        bind:value={$formEdit.student_CI}
                        error={$formEdit.errors?.student_CI}
                    />
                    <Input
                        type="tel"
                        required={true}
                        label={"Teléfono"}
                        bind:value={$formEdit.student_phone_number}
                        error={$formEdit.errors?.student_phone_number}
                    />
                    <Input
                        type="select"
                        required={true}
                        label={"Sexo"}
                        bind:value={$formEdit.student_sex}
                        error={$formEdit.errors?.student_sex}
                    >
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </Input>
                    <Input
                        type="select"
                        required={true}
                        label={"Año escolar"}
                        bind:value={$formEdit.course_id}
                        error={$formEdit.errors?.course_id}
                    >
                        <option value="Masculino">1er año</option>
                        <option value="Femenino">2do año</option>
                    </Input>
                    <Input
                        type="select"
                        required={true}
                        label={"Sección"}
                        bind:value={$formEdit.section_id}
                        error={$formEdit.errors?.section_id}
                    >
                        <option value="Masculino">A</option>
                        <option value="Femenino">B</option>
                    </Input>

                    <Input
                        type="textarea"
                        required={true}
                        label={"Colegio de procedencia"}
                        bind:value={$formCreate.student_previous_school}
                        error={$formCreate.errors?.student_previous_school}
                    />
                </fieldset>
            </Carousel>
        </form>
        <input
            form="a-form"
            slot="btn_footer"
            type="submit"
            value={$formEdit.processing ? "Cargando..." : "Editar"}
            class="hover:bg-color3 hover:text-white duration-200 mt-auto w-full bg-color2 text-black font-bold py-3 rounded-md cursor-pointer"
        />
    </Modal>
{/if}

<div class="flex justify-between">
    <button
        class="btn_create"
        on:click={(e) => {
            e.preventDefault();
            showModal = true;
        }}>Inscribir</button
    >
</div>


<Table
    {selectedRow}
   
    on:fillFormToEdit={fillFormToEdit}
    on:clickDeleteIcon={() => {
        handleDelete(selectedRow.id);
    }}
>
    <thead slot="thead" class="sticky top-0 z-50">
        <tr>
            <th>Cod</th>
            <th>Actividad</th>
            <th>Estado</th>
            <th>Ubicación SSF</th>
            <th>Dirección/Oficina </th>
            <th>Progreso</th>
            <th>Fecha</th>
        </tr>
    </thead>

    <tbody slot="tbody">
       <tr>
        <td>12</td>
        <td>3</td>
        <td>4</td>
        <td>5</td>
        <td>6</td>
        <td>7</td>
       </tr>
    </tbody>
</Table>
