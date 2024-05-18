<script>
    import Table from "../../components/Table.svelte";
    import Modal from "../../components/Modal.svelte";
    import Input from "../../components/Input.svelte";
    import Alert from "../../components/Alert.svelte";
    import { displayAlert } from "../../stores/alertStore";
    import { useForm } from "@inertiajs/svelte";

    let formCreate = useForm({
        type_activity_id: "",
        area_id: "",
        status_id: "",
        title: "",
        user_id: "",
        start_date: "",
        end_date: "",
        location_id: "",
        office_id: "",
        division_id: "",
        department_id: "",
        progress: "",
        observation: "",
    });

    let formEdit = useForm({
        type_activity_id: "",
        area_id: "",
        status_id: "",
        title: "",
        user_id: "",
        start_date: "",
        end_date: "",
        location_id: "",
        office_id: "",
        division_id: "",
        department_id: "",
        progress: "",
        observation: "",
    });

    $: showModal = false;
    $: showModalFormEdit = false
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
                $formCreate.reset()
                displayAlert({ type: "success", message: 'Ok todo salió bien' });
                showModal = false
                
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
                $formEdit.reset()
                displayAlert({ type: "success", message: 'Ok todo salió bien' });
                showModalFormEdit = false
                selectedRow = {status: false, id:0, row: {}}
                
            },
        });
    }
    function handleDelete(id) {
        $formCreate.delete(`/dashboard/bitacora/${id}`, {
            onBefore: () => confirm(`¿Está seguro de eliminar la actividad ${selectedRow.title}?`)
        })
    }
    export let data = [];


    function fillFormToEdit() {
        $formEdit.reset()
        showModalFormEdit = true
    }

</script>

<Alert />

{#if showModal}
<Modal bind:showModal>
    <h2 slot="header" class="text-sm text-center">CREAR ACTIVIDAD</h2>

    <form
        id="a-form"
        on:submit={handleSubmit}
        action=""
        class="w-[500px] grid grid-cols-2 gap-x-5"
    >
        <Input
            type="text"
            required={true}
            label={"Nombre de la actividad"}
            bind:value={$formCreate.title}
            error={$formCreate.errors?.title}
        />
        <Input
            type="select"
            required={true}
            label={"Tipo de actividad"}
            bind:value={$formCreate.type_activity_id}
            error={$formCreate.errors?.type_activity_id}
        >
            {#each data.type_activities as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>

        <Input
            type="select"
            required={true}
            label={"Ubicación SSF"}
            bind:value={$formCreate.location_id}
            error={$formCreate.errors?.location_id}
        >
            {#each data.locations as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>

        <Input
            type="select"
            required={true}
            label={"Dirección/Oficina"}
            bind:value={$formCreate.office_id}
            error={$formCreate.errors?.office_id}
        >
            {#each data.offices as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>

        <Input
            type="select"
            required={true}
            label={"División"}
            bind:value={$formCreate.division_id}
            error={$formCreate.errors?.division_id}
        >
            {#each data.divisions as option}
                {#if option.office_id == $formCreate.office_id || option.id == 1}
                    <option value={option.id}>{option.name}</option>
                {/if}
            {/each}
        </Input>
        <Input
            type="select"
            required={true}
            label={"Departamento"}
            bind:value={$formCreate.department_id}
            error={$formCreate.errors?.department_id}
        >
            {#each data.departments as option}
                {#if (option.division_id == $formCreate.division_id && option.division.office_id == $formCreate.office_id) || option.id == 1}
                    <option value={option.id}>{option.name}</option>
                {/if}
            {/each}
        </Input>

        <Input
            type="date"
            required={true}
            label={"Fecha de inicio"}
            bind:value={$formCreate.start_date}
            error={$formCreate.errors?.start_date}
        />
        <Input
            type="date"
            label={"Fecha de finalización"}
            bind:value={$formCreate.end_date}
            error={$formCreate.errors?.end_date}
        />

        <Input
            type="number"
            label={"Progreso"}
            bind:value={$formCreate.progress}
            error={$formCreate.errors?.progress}
        />
        <Input
            type="select"
            required={true}
            label={"Estado"}
            bind:value={$formCreate.status_id}
            error={$formCreate.errors?.status_id}
        >
            {#each data.status as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>
        <Input
            type="select"
            required={true}
            label={"Area"}
            bind:value={$formCreate.area_id}
            error={$formCreate.errors?.area_id}
        >
            {#each data.areas as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>
        <Input
            type="textarea"
            name=""
            id=""
            label={"Observación"}
            bind:value={$formCreate.observation}
            error={$formCreate.errors?.observation}
        />
    </form>
    <input
        form="a-form"
        slot="btn_footer"
        type="submit"
        value={$formCreate.processing ? "Cargando..." : "Guardar"}
        class="hover:bg-color3 hover:text-white duration-200 mt-auto w-full bg-color2 text-black font-bold py-3 rounded-md cursor-pointer"
    />
</Modal>
{/if}


{#if showModalFormEdit}
<Modal bind:showModal={showModalFormEdit}>
    <h2 slot="header" class="text-sm text-center">EDITAR ACTIVIDAD</h2>

    <form
        id="a-form"
        on:submit={handleEdit}
        action=""
        class="w-[500px] grid grid-cols-2 gap-x-5"
    >
        <Input
            type="text"
            required={true}
            label={"Nombre de la actividad"}
            bind:value={$formEdit.title}
            error={$formEdit.errors?.title}
        />
        <Input
            type="select"
            required={true}
            label={"Tipo de actividad"}
            bind:value={$formEdit.type_activity_id}
            error={$formEdit.errors?.type_activity_id}
        >
            {#each data.type_activities as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>

        <Input
            type="select"
            required={true}
            label={"Ubicación SSF"}
            bind:value={$formEdit.location_id}
            error={$formEdit.errors?.location_id}
        >
            {#each data.locations as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>

        <Input
            type="select"
            required={true}
            label={"Dirección/Oficina"}
            bind:value={$formEdit.office_id}
            error={$formEdit.errors?.office_id}
        >
            {#each data.offices as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>

        <Input
            type="select"
            required={true}
            label={"División"}
            bind:value={$formEdit.division_id}
            error={$formEdit.errors?.division_id}
        >
            {#each data.divisions as option}
                {#if option.office_id == $formEdit.office_id || option.id == 1}
                    <option value={option.id}>{option.name}</option>
                {/if}
            {/each}
        </Input>
        <Input
            type="select"
            required={true}
            label={"Departamento"}
            bind:value={$formEdit.department_id}
            error={$formEdit.errors?.department_id}
        >
            {#each data.departments as option}
                {#if (option.division_id == $formEdit.division_id && option.division.office_id == $formEdit.office_id) || option.id == 1}
                    <option value={option.id}>{option.name}</option>
                {/if}
            {/each}
        </Input>

        <Input
            type="date"
            required={true}
            label={"Fecha de inicio"}
            bind:value={$formEdit.start_date}
            error={$formEdit.errors?.start_date}
        />
        <Input
            type="date"
            label={"Fecha de finalización"}
            bind:value={$formEdit.end_date}
            error={$formEdit.errors?.end_date}
        />

        <Input
            type="number"
            label={"Progreso"}
            bind:value={$formEdit.progress}
            error={$formEdit.errors?.progress}
        />
        <Input
            type="select"
            required={true}
            label={"Estado"}
            bind:value={$formEdit.status_id}
            error={$formEdit.errors?.status_id}
        >
            {#each data.status as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>
        <Input
            type="select"
            required={true}
            label={"Area"}
            bind:value={$formEdit.area_id}
            error={$formEdit.errors?.area_id}
        >
            {#each data.areas as option}
                <option value={option.id}>{option.name}</option>
            {/each}
        </Input>
        <Input
            type="textarea"
            name=""
            id=""
            label={"Observación"}
            bind:value={$formEdit.observation}
            error={$formEdit.errors?.observation}
        />
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
    <h1>Bitácora</h1>

    <button class="btn_create " on:click={() => {
        showModal = true

    }}
        >Nueva actividad</button
    >
</div>

<Table
    {selectedRow}
    serverSideData={{
        ...data.activities,
        data: "",
        filters: data.filters,
    }}
    filtersOptions={{status: data.status}}
    on:fillFormToEdit={fillFormToEdit}
    on:clickDeleteIcon={() => {
        handleDelete(selectedRow.id)

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
            <th>Iniciada en</th>
            <th>Terminada en</th>
        </tr>
    </thead>

    <tbody slot="tbody">
        {#each data.activities.data as row}
            <tr
                on:click={(e) => {
                    // let newSelectedRowStatus = !selectedRow.status;
                    if (row.id != selectedRow.id ) {
                        selectedRow = {
                            status: true,
                            id: row.id,
                            title: row.title
                        };
                        $formEdit.defaults({
                            ...row,
                        });
                    } else {
                        selectedRow = {
                            status: false,
                            id: 0,
                            title: '',

                        };
                        $formEdit.defaults({
                            type_activity_id: "",
                            area_id: "",
                            status_id: "",
                            title: "",
                            user_id: "",
                            start_date: "",
                            end_date: "",
                            location_id: "",
                            office_id: "",
                            division_id: "",
                            department_id: "",
                            progress: "",
                            observation: "",
                        });
                    }
                }}
                class={`cursor-pointer hover:bg-grayBlue hover:bg-opacity-5 ${selectedRow.id == row.id ? "bg-grayBlue hover:bg-opacity-10 bg-opacity-10 brightness-110" : ""}`}
            >
                <td>{row.code}</td>
                <td>{row.title}</td>
                <td>
                    <div
                        class={`inline px-3 py-1 text-sm  rounded-full dark:bg-gray-800  gap-x-2 font-light tracking-wide`}
                        class:text-ligthGreen={row.status_id == "2"}
                        class:dark:text-redLight={row.status_id == "3"}
                    >
                        {row.status_name}
                    </div>
                </td>
                <td>{row.location_name}</td>
                <td>{row.office_name}</td>
                <td>
                    <p class="text-center text-xs text-gray-300">
                        {row.progress}%
                    </p>
                    <progress id="file" value={100 - row.progress} max="100">
                        {row.progress}%
                    </progress>
                </td>
                <td>{row.today_date}</td>
                <td>{row.start_date}</td>
                <td>{row.end_date === null ? "Sin Finalizar" : row.end_date}</td
                >
            </tr>
        {/each}
    </tbody>
</Table>
