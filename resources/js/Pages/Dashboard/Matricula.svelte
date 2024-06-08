<script>
    import Table from "../../components/Table.svelte";
    import Modal from "../../components/Modal.svelte";
    import Input from "../../components/Input.svelte";
    import Alert from "../../components/Alert.svelte";
    import { displayAlert } from "../../stores/alertStore";
    import { useForm, router, page } from "@inertiajs/svelte";
    import { claim_svg_element } from "svelte/internal";
    export let data = [];

    const emptyDataForm = {
        student_id: "",
        student_name: "",
        student_last_name: "",
        student_date_birth: "",
        student_email: "",
        student_ci: "",
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
        rep_ci: "",
        rep_phone_number: "",
        rep_email: "",
        rep_profession: "",
        rep_workplace: "",
        second_rep_name: "",
        second_rep_last_name: "",
        second_rep_ci: "",
        second_rep_phone_number: "",
        second_rep_email: "",
        second_rep_profession: "",
        second_rep_workplace: "",
        rep_id: "",
    };

    $: sectionsOfThisYear =
        data.course_sections?.data?.[`course_${data.filters.course_id}`];
    $: lastSectionId = sectionsOfThisYear?.[sectionsOfThisYear?.length - 1].id;
    let formCreate = useForm({
        student_name: "Nombre de estudiante",
        student_last_name: "Villasmil Tovar",
        student_date_birth: "1999-06-25",
        student_email: "juanvillans16@gmail.com",
        student_ci: "27253194",
        student_phone_number: "04124393123",
        course_id: 1,
        section_id: "",
        student_sex: "Masculino",
        student_previous_school: "Libertadores de america jeje",
        state: "Falcón",
        city: "Coro",
        address: "Conjunto residencial Juan crisóstomo Falcón",
        rep_name: "Mildred",
        rep_last_name: "Tovar",
        rep_ci: "385560",
        rep_phone_number: "048271281",
        rep_email: "msocratis@gmail.com",
        rep_profession: "Economista",
        rep_workplace: "Fundefal",
        second_rep_name: "",
        second_rep_last_name: "",
        second_rep_ci: "",
        second_rep_phone_number: "",
        second_rep_email: "",
        second_rep_profession: "",
        second_rep_workplace: "",
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
        $formEdit.put(`/dashboard/matricula/${$formEdit.student_id}`, {
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
        $formCreate.delete(`/dashboard/matricula/${id}`, {
            onBefore: () =>
                confirm(`¿Está seguro de eliminar a este estudiante?`),
        });
    }

    function fillFormToEdit() {
        $formEdit.reset();
        showModalFormEdit = true;
    }

    function createSection() {
        router.post(
            "/dashboard/secciones",
            { course_id: data.filters.course_id, section_id: lastSectionId },
            {
                onError: (errors) => {
                    if (errors.data) {
                        displayAlert({ type: "error", message: errors.data });
                    }
                },
                onSuccess: (mensaje) => {
                    displayAlert({
                        type: "success",
                        message: "Ok todo salió bien",
                    });
                },
            },
        );
    }

    function deleteSection() {
        router.delete(
            `/dashboard/secciones/${data.filters.course_id}/${lastSectionId}`,
            {
                onBefore: () =>
                    confirm(`¿Está seguro de eliminar esta sección?`),
            },
        );
    }
    function changeYear(course_id) {
        router.get($page.url, { course_id, section_id: 1 });
    }

    function search_rep1(ci) {
        router.get(
            `/dashboard/matricula/search-representative/${ci}`, {},
            {
                only: ['representative'],
                onProgress: (progress) => {},
                onSuccess: (page) => { console.log(page, data)},
                onError: (errors) => {},
            },
        );
    }

    function search_second(ci) {
        router.get(`/dashboard/matricula/search-second_representative/`, {
            ci,
        });
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
            class="px-5 bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
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
                error={$formCreate.errors?.student_last_name}
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
                label="correo"
                bind:value={$formCreate.student_email}
                error={$formCreate.errors?.student_email}
            />
            <Input
                type="number"
                label={"Cédula"}
                bind:value={$formCreate.student_ci}
                error={$formCreate.errors?.student_ci}
            />
            <Input
                type="tel"
                label={"Teléfono"}
                bind:value={$formCreate.student_phone_number}
                error={$formCreate.errors?.student_phone_number}
            />
            <Input
                type="select"
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
                {#each data.courses as course}
                    <option value={course.id}>{course.name}</option>
                {/each}
            </Input>
            <Input
                type="select"
                required={true}
                label={"Sección"}
                bind:value={$formCreate.section_id}
                error={$formCreate.errors?.section_id}
            >
                {#each data.course_sections?.data?.[`course_${$formCreate.course_id}`] as section}
                    <option value={section.id}>{section.name}</option>
                {/each}
            </Input>

            <Input
                type="textarea"
                label={"Colegio de procedencia"}
                bind:value={$formCreate.student_previous_school}
                error={$formCreate.errors?.student_previous_school}
            />
        </fieldset>

        <fieldset
            class="px-5 bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >DIRECCIÓNES</legend
            >
            <Input
                type="text"
                label={"Estado"}
                bind:value={$formCreate.state}
                error={$formCreate.errors?.state}
            />
            <Input
                type="text"
                label={"Ciudad"}
                bind:value={$formCreate.city}
                error={$formCreate.errors?.city}
            />
            <Input
                type="textarea"
                label={"Dirección específica"}
                bind:value={$formCreate.address}
                error={$formCreate.errors?.address}
                classes="col-span-2"
            />
        </fieldset>

        <fieldset
            class="px-5 bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >REPRESENTANTE LEGAL</legend
            >
            <Input
                type="number"
                required={true}
                label={"Cédula"}
                bind:value={$formCreate.rep_ci}
                error={$formCreate.errors?.rep_ci}
                on:input={(e) => search_rep1(e.target.value)}
            />
            <Input
                type="text"
                required={true}
                label={"Nombres"}
                bind:value={$formCreate.rep_name}
                error={$formCreate.errors?.rep_name}
            />
            <Input
                type="text"
                required={true}
                label={"Apellidos"}
                bind:value={$formCreate.rep_last_name}
                error={$formCreate.errors?.rep_last_name}
            />

            <Input
                type="date"
                label={"Fecha de nacimiento"}
                bind:value={$formCreate.rep_date_birth}
                error={$formCreate.errors?.rep_date_birth}
            />
            <Input
                type="email"
                required={true}
                label="correo"
                bind:value={$formCreate.rep_email}
                error={$formCreate.errors?.rep_email}
            />
            <Input
                type="tel"
                required={true}
                label={"Teléfono"}
                bind:value={$formCreate.rep_phone_number}
                error={$formCreate.errors?.rep_phone_number}
            />

            <Input
                type="text"
                label={"Profesión"}
                bind:value={$formCreate.rep_profession}
                error={$formCreate.errors?.rep_profession}
            />

            <Input
                type="textarea"
                label={"Lugar de trabajo"}
                bind:value={$formCreate.rep_workplace}
                error={$formCreate.errors?.rep_workplace}
            />
        </fieldset>

        <fieldset
            class="px-5 bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >2DO REPRESENTANTE</legend
            >
            <Input
                type="number"
                label={"Cédula"}
                bind:value={$formCreate.second_rep_ci}
                error={$formCreate.errors?.second_rep_ci}
                on:input={() => console.log("2")}
            />
            <Input
                type="text"
                label={"Nombres"}
                bind:value={$formCreate.second_rep_name}
                error={$formCreate.errors?.second_rep_name}
            />
            <Input
                type="text"
                label={"Apellidos"}
                bind:value={$formCreate.second_rep_last_name}
                error={$formCreate.errors?.second_rep_last_name}
            />
            <Input
                type="date"
                label={"Fecha de nacimiento"}
                bind:value={$formCreate.second_rep_date_birth}
                error={$formCreate.errors?.second_rep_date_birth}
            />
            <Input
                type="email"
                label="correo"
                bind:value={$formCreate.second_rep_email}
                error={$formCreate.errors?.second_rep_email}
            />

            <Input
                type="tel"
                label={"Teléfono"}
                bind:value={$formCreate.second_rep_phone_number}
                error={$formCreate.errors?.second_rep_phone_number}
            />

            <Input
                type="text"
                label={"Profesión"}
                bind:value={$formCreate.second_rep_profession}
                error={$formCreate.errors?.second_rep_profession}
            />

            <Input
                type="textarea"
                label={"Lugar de trabajo"}
                bind:value={$formCreate.second_rep_workplace}
                error={$formCreate.errors?.second_rep_workplace}
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

<Modal bind:showModal={showModalFormEdit}>
    <h2 slot="header" class="text-sm text-center">EDITAR ACTIVIDAD</h2>

    <form id="a-form" on:submit={handleEdit} action="" class="w-[600px]">
        <fieldset
            class="px-5 bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >DATOS DEL ESTUDIANTE</legend
            >
            <Input
                type="text"
                required={true}
                label={"Nombres"}
                bind:value={$formEdit.student_name}
                error={$formEdit.errors?.student_name}
            />
            <Input
                type="text"
                required={true}
                label={"Apellidos"}
                bind:value={$formEdit.student_last_name}
                error={$formEdit.errors?.student_last_name}
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
                label="correo"
                bind:value={$formEdit.student_email}
                error={$formEdit.errors?.student_email}
            />
            <Input
                type="number"
                label={"Cédula"}
                required={true}
                bind:value={$formEdit.student_ci}
                error={$formEdit.errors?.student_ci}
            />
            <Input
                type="tel"
                label={"Teléfono"}
                bind:value={$formEdit.student_phone_number}
                error={$formEdit.errors?.student_phone_number}
            />
            <Input
                type="select"
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
                {#each data.courses as course}
                    <option value={course.id}>{course.name}</option>
                {/each}
            </Input>
            <Input
                type="select"
                required={true}
                label={"Sección"}
                bind:value={$formEdit.section_id}
                error={$formEdit.errors?.section_id}
            >
                {#each data.course_sections?.data?.[`course_${$formEdit.course_id}`] as section}
                    <option value={section.id}>{section.name}</option>
                {/each}
            </Input>

            <Input
                type="textarea"
                label={"Colegio de procedencia"}
                bind:value={$formEdit.student_previous_school}
                error={$formEdit.errors?.student_previous_school}
            />
        </fieldset>

        <fieldset
            class="px-5 bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >DIRECCIÓNES</legend
            >
            <Input
                type="text"
                label={"Estado"}
                bind:value={$formEdit.state}
                error={$formEdit.errors?.state}
            />
            <Input
                type="text"
                label={"Ciudad"}
                bind:value={$formEdit.city}
                error={$formEdit.errors?.city}
            />
            <Input
                type="textarea"
                label={"Dirección específica"}
                bind:value={$formEdit.address}
                error={$formEdit.errors?.address}
                classes="col-span-2"
            />
        </fieldset>

        <fieldset
            class="px-5 bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >REPRESENTANTE LEGAL</legend
            >
            <Input
                type="text"
                required={true}
                label={"Nombre"}
                bind:value={$formEdit.rep_name}
                error={$formEdit.errors?.rep_name}
            />
            <Input
                type="text"
                required={true}
                label={"Apellido"}
                bind:value={$formEdit.rep_last_name}
                error={$formEdit.errors?.rep_last_name}
            />
            <Input
                type="number"
                required={true}
                label={"Cédula"}
                bind:value={$formEdit.rep_ci}
                error={$formEdit.errors?.rep_ci}
            />
            <Input
                type="date"
                label={"Fecha de nacimiento"}
                bind:value={$formEdit.rep_date_birth}
                error={$formEdit.errors?.rep_date_birth}
            />
            <Input
                type="email"
                required={true}
                label="correo"
                bind:value={$formEdit.rep_email}
                error={$formEdit.errors?.rep_email}
            />
            <Input
                type="tel"
                required={true}
                label={"Teléfono"}
                bind:value={$formEdit.rep_phone_number}
                error={$formEdit.errors?.rep_phone_number}
            />

            <Input
                type="text"
                label={"Profesión"}
                bind:value={$formEdit.rep_profession}
                error={$formEdit.errors?.rep_profession}
            />

            <Input
                type="textarea"
                label={"Lugar de trabajo"}
                bind:value={$formEdit.rep_workplace}
                error={$formEdit.errors?.rep_workplace}
            />
        </fieldset>

        <fieldset
            class="px-5 bg-black bg-opacity-10 mt-4 grid grid-cols-2 gap-x-5 w-full border p-6 pt-2 border-color2 rounded-md"
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >2DO REPRESENTANTE</legend
            >
            <Input
                type="text"
                label={"Nombre"}
                bind:value={$formEdit.second_rep_name}
                error={$formEdit.errors?.second_rep_name}
            />
            <Input
                type="text"
                label={"Apellido"}
                bind:value={$formEdit.second_rep_last_name}
                error={$formEdit.errors?.second_rep_last_name}
            />
            <Input
                type="date"
                label={"Fecha de nacimiento"}
                bind:value={$formEdit.second_rep_date_birth}
                error={$formEdit.errors?.second_rep_date_birth}
            />
            <Input
                type="email"
                label="correo"
                bind:value={$formEdit.second_rep_email}
                error={$formEdit.errors?.second_rep_email}
            />
            <Input
                type="number"
                label={"Cédula"}
                bind:value={$formEdit.second_rep_ci}
                error={$formEdit.errors?.second_rep_ci}
            />
            <Input
                type="tel"
                label={"Teléfono"}
                bind:value={$formEdit.second_rep_phone_number}
                error={$formEdit.errors?.second_rep_phone_number}
            />

            <Input
                type="text"
                label={"Profesión"}
                bind:value={$formEdit.second_rep_profession}
                error={$formEdit.errors?.second_rep_profession}
            />

            <Input
                type="textarea"
                label={"Lugar de trabajo"}
                bind:value={$formEdit.second_rep_workplace}
                error={$formEdit.errors?.second_rep_workplace}
            />
        </fieldset>
    </form>
    <input
        form="a-form"
        slot="btn_footer"
        type="submit"
        value={$formEdit.processing ? "Cargando..." : "Editar"}
        class="hover:bg-color3 hover:text-white duration-200 mt-auto w-full bg-color2 text-black font-bold py-3 rounded-md cursor-pointer"
    />
</Modal>

<div class="flex justify-between items-center">
    <div class="w-44">
        <label for="filterYear " class="text-lg"> Año escolar </label>
        <select
            id="filterYear"
            class="w-full p-2 rounded-xl"
            on:change={(e) => changeYear(e.target.value)}
        >
            {#each data.courses as course}
                <option class="bg-gray-50" value={course.id}
                    >{course.name}</option
                >
            {/each}
        </select>
    </div>
    <button
        class="btn_create inline-block"
        on:click={(e) => {
            e.preventDefault();
            $formCreate.section_id = +data.filters.section_id;
            $formCreate.course_id = +data.filters.course_id;
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
    serverSideData={{ filters: data.filters }}
    filtersOptions={{ section_id: sectionsOfThisYear }}
    pagination={false}
>
    <div slot="filterBox">
        {#if lastSectionId < 6}
            <button
                on:click={() => createSection()}
                class="rounded border border-color3 text-color3 h-full cursor-pointer hover:bg-color3 hover:text-gray-100 px-4"
            >
                Crear sección
            </button>
        {/if}

        {#if sectionsOfThisYear.length !== 1 && lastSectionId == data.filters.section_id}
            <button
                on:click={() => deleteSection(data.filters.section_id)}
                class="ml-3 p-2 px-3 bg-gray-100"
                title="Elimar Sección"
            >
                <iconify-icon class="text-xl relative top-1" icon="ph:trash"
                ></iconify-icon>
            </button>
        {/if}
    </div>
    <thead slot="thead" class="sticky top-0 z-50">
        <tr>
            <th>N°</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>C.I</th>
            <th>Sexo</th>
            <th>Edad</th>
            <th>Rep Legal</th>
            <th>Tel rep legal</th>
        </tr>
    </thead>

    <tbody slot="tbody">
        {#each data.students.data as row, i}
            <tr
                on:click={(e) => {
                    // let newSelectedRowStatus = !selectedRow.status;
                    if (row.student_id != selectedRow.id) {
                        selectedRow = {
                            status: true,
                            id: row.student_id,
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
                class={`cursor-pointer  ${selectedRow.id == row.student_id ? "bg-color2 hover:bg-opacity-10 bg-opacity-10 brightness-110" : " hover:bg-gray-500 hover:bg-opacity-5"}`}
            >
                <td>{i + 1}</td>
                <td>{row.student_name}</td>
                <td>{row.student_last_name}</td>
                <td>{row.student_ci}</td>
                <td>{row.student_sex}</td>
                <td>{row.student_age}</td>
                <td>{row.rep_name} {row.rep_last_name}</td>
                <td>{row.rep_phone_number}</td>
            </tr>
        {/each}
    </tbody>
</Table>
