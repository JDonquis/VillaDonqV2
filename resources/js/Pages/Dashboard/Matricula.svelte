<script>
    import Table from "../../components/Table.svelte";
    import Modal from "../../components/Modal.svelte";
    import Input from "../../components/Input.svelte";
    import axios from "axios";
    import debounce from "lodash/debounce";

    import Alert from "../../components/Alert.svelte";
    import { displayAlert } from "../../stores/alertStore";
    import { useForm, router, page } from "@inertiajs/svelte";
    import { claim_svg_element } from "svelte/internal";
    export let data = [];

    $: selectedCourseId = (data.filters?.course_id || "1").toString();

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

    let form = useForm({
        student_name: "",
        student_last_name: "",
        student_date_birth: "",
        student_email: "",
        student_ci: "",
        student_phone_number: "",
        course_id: 1,
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
    });

    let submitStatus = "Crear";
    let editingStudentId = null;

    let showModal = false;
    let selectedRow = { status: false, id: 0 };

    document.addEventListener("keydown", ({ key }) => {
        if (key === "Escape") {
            selectedRow = { status: false, id: 0 };
        }
    });

    function handleSubmit(event) {
        event.preventDefault();
        if (submitStatus === "Crear") {
            $form.clearErrors();
            $form.post("/dashboard/matricula", {
                onError: (errors) => {
                    if (errors.data) {
                        displayAlert({ type: "error", message: errors.data });
                    }
                },
                onSuccess: () => {
                    $form.reset();
                    displayAlert({
                        type: "success",
                        message: "Estudiante creado correctamente",
                    });
                    showModal = false;
                },
            });
        } else if (submitStatus === "Editar") {
            $form.clearErrors();
            $form.put(`/dashboard/matricula/${editingStudentId}`, {
                onError: (errors) => {
                    if (errors.data) {
                        displayAlert({ type: "error", message: errors.data });
                    }
                },
                onSuccess: () => {
                    $form.reset();
                    displayAlert({
                        type: "success",
                        message: "Estudiante actualizado correctamente",
                    });
                    showModal = false;
                    submitStatus = "Crear";
                    editingStudentId = null;
                    selectedRow = { status: false, id: 0 };
                },
            });
        }
    }

    function handleDelete(id) {
        $form.delete(`/dashboard/matricula/${id}`, {
            onBefore: () =>
                confirm(`¿Está seguro de eliminar a este estudiante?`),
        });
    }

    function fillFormToEdit() {
        showModal = true;

        console.log(selectedRow);
        const student = selectedRow.data;
        submitStatus = "Editar";
        editingStudentId = student.student_id;
        $form.student_name = student.student_name;
        $form.student_last_name = student.student_last_name;
        $form.student_date_birth = student.student_date_birth;
        $form.student_email = student.student_email;
        $form.student_ci = student.student_ci;
        $form.student_phone_number = student.student_phone_number;
        $form.course_id = student.course_id;
        $form.section_id = student.section_id;
        $form.student_sex = student.student_sex;
        $form.student_previous_school = student.previous_school;
        $form.state = student.state;
        $form.city = student.city;
        $form.address = student.address;
        $form.rep_name = student.rep_name;
        $form.rep_last_name = student.rep_last_name;
        $form.rep_ci = student.rep_ci;
        $form.rep_phone_number = student.rep_phone_number;
        $form.rep_email = student.rep_email;
        $form.rep_profession = student.rep_profession;
        $form.rep_workplace = student.rep_workplace;
        showModal = true;
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
        console.log("Cambiando curso a:", course_id);
        const params = {
            ...data.filters,
            course_id: course_id,
            section_id: 1, // Reset section to 1 when year changes
        };
        router.get(window.location.pathname, params, {
            preserveState: false, // Ensure we get fresh data
            replace: true,
        });
    }

    const search_rep1 = debounce(async (ci) => {
        try {
            const response = await axios.get(
                `/dashboard/matricula/search-representative/${ci}`,
            );
        } catch (error) {}
    }, 300);

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

<Modal bind:showModal classes={"w-fit"}>
    <form
        id="a-form"
        on:submit={handleSubmit}
        action=""
        class="max-w-[1260px] gap-10 flex justify-around pt-2 px-7"
    >
        <fieldset
            class="  border-3 medium-shadow border-black pb-9 px-5 bg-gray-50 grid grid-cols-2 gap-x-10 h-fit md:px-9 md:pt-2"
        >
            <legend class="text-center px-5 font-bold rounded-sm bg"
                >DATOS DEL ESTUDIANTE</legend
            >
            <Input
                type="text"
                required={true}
                label={"Nombres"}
                bind:value={$form.student_name}
                error={$form.errors?.student_name}
            />
            <Input
                type="text"
                required={true}
                label={"Apellidos"}
                bind:value={$form.student_last_name}
                error={$form.errors?.student_last_name}
            />
            <Input
                type="date"
                required={true}
                label={"Fecha de nacimiento"}
                bind:value={$form.student_date_birth}
                error={$form.errors?.student_date_birth}
            />
            <Input
                type="email"
                label="Correo"
                bind:value={$form.student_email}
                error={$form.errors?.student_email}
            />
            <Input
                type="number"
                required={true}
                label={"Cédula"}
                bind:value={$form.student_ci}
                error={$form.errors?.student_ci}
            />
            <Input
                type="tel"
                label={"Teléfono"}
                bind:value={$form.student_phone_number}
                error={$form.errors?.student_phone_number}
            />
            <Input
                type="select"
                label={"Sexo"}
                bind:value={$form.student_sex}
                error={$form.errors?.student_sex}
            >
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
            </Input>
            <Input
                type="select"
                required={true}
                label={"Año escolar"}
                bind:value={$form.course_id}
                error={$form.errors?.course_id}
            >
                {#each data.courses as course}
                    <option value={course.id}>{course.name}</option>
                {/each}
            </Input>
            <Input
                type="select"
                required={true}
                label={"Sección"}
                bind:value={$form.section_id}
                error={$form.errors?.section_id}
            >
                {#each data.course_sections?.data?.[`course_${$form.course_id}`] as section}
                    <option value={section.id}>{section.name}</option>
                {/each}
            </Input>

            <Input
                type="textarea"
                label={"Colegio de procedencia"}
                bind:value={$form.student_previous_school}
                error={$form.errors?.student_previous_school}
            />
        </fieldset>

        <div>
            <fieldset
                class=" border-3 medium-shadow border-black pb-9 px-5 bg-gray-50 grid grid-cols-2 gap-x-10 h-fit md:px-9 md:pt-2"
            >
                <legend class="text-center px-5 font-bold rounded-sm bg"
                    >REPRESENTANTE LEGAL</legend
                >
                <Input
                    type="number"
                    required={true}
                    label={"Cédula"}
                    bind:value={$form.rep_ci}
                    error={$form.errors?.rep_ci}
                    on:input={(e) => search_rep1(e.target.value)}
                />
                <Input
                    type="text"
                    required={true}
                    label={"Nombres"}
                    bind:value={$form.rep_name}
                    error={$form.errors?.rep_name}
                />
                <Input
                    type="text"
                    required={true}
                    label={"Apellidos"}
                    bind:value={$form.rep_last_name}
                    error={$form.errors?.rep_last_name}
                />

                <Input
                    type="text"
                    label={"Parentesco"}
                    bind:value={$form.rep_relationship}
                    error={$form.errors?.rep_relationship}
                />

                <!-- <Input
                    type="date"
                    label={"Fecha de nacimiento"}
                    bind:value={$form.rep_date_birth}
                    error={$form.errors?.rep_date_birth}
                /> -->
                <Input
                    type="email"
                    required={true}
                    label="Correo"
                    bind:value={$form.rep_email}
                    error={$form.errors?.rep_email}
                />
                <Input
                    type="tel"
                    required={false}
                    label={"Teléfono"}
                    bind:value={$form.rep_phone_number}
                    error={$form.errors?.rep_phone_number}
                />

                <!-- <Input
                    type="text"
                    label={"Profesión"}
                    bind:value={$form.rep_profession}
                    error={$form.errors?.rep_profession}
                />

                <Input
                    type="textarea"
                    label={"Lugar de trabajo"}
                    bind:value={$form.rep_workplace}
                    error={$form.errors?.rep_workplace}
                /> -->
            </fieldset>

            <fieldset
                class=" border-3 medium-shadow border-black pb-9 px-5 mt-9 bg-gray-50 grid grid-cols-2 gap-x-10 h-fit md:px-9 md:pt-2"
            >
                <legend class="text-center px-5 font-bold rounded-sm bg"
                    >SEGUNDO REPRESENTANTE</legend
                >

                <Input
                    type="number"
                    label={"Cédula"}
                    bind:value={$form.second_rep_ci}
                    error={$form.errors?.second_rep_ci}
                    on:input={() => console.log("2")}
                />
                <Input
                    type="text"
                    label={"Nombres"}
                    bind:value={$form.second_rep_name}
                    error={$form.errors?.second_rep_name}
                />
                <Input
                    type="text"
                    label={"Apellidos"}
                    bind:value={$form.second_rep_last_name}
                    error={$form.errors?.second_rep_last_name}
                />

                <Input
                    type="text"
                    label={"Parentesco"}
                    bind:value={$form.second_rep_relationship}
                    error={$form.errors?.second_rep_relationship}
                />
                <!-- <Input
                    type="date"
                    label={"Fecha de nacimiento"}
                    bind:value={$form.second_rep_date_birth}
                    error={$form.errors?.second_rep_date_birth}
                /> -->
                <Input
                    type="email"
                    label="Correo"
                    bind:value={$form.second_rep_email}
                    error={$form.errors?.second_rep_email}
                />

                <Input
                    type="tel"
                    label={"Teléfono"}
                    bind:value={$form.second_rep_phone_number}
                    error={$form.errors?.second_rep_phone_number}
                />

                <!-- <Input
                    type="text"
                    label={"Profesión"}
                    bind:value={$form.second_rep_profession}
                    error={$form.errors?.second_rep_profession}
                />

                <Input
                    type="textarea"
                    label={"Lugar de trabajo"}
                    bind:value={$form.second_rep_workplace}
                    error={$form.errors?.second_rep_workplace}
                /> -->
            </fieldset>
        </div>
        <!-- <fieldset
            class="px-5 bg-gray-50 mt-4 grid grid-cols-2 gap-x-10  w-full border md:p-9 pt-2  "
        >
            <legend
                class="text-center px-5 py-1 rounded-sm bg-color2 text-gray-100"
                >DIRECCION DE HABITACION</legend
            >
            <Input
                type="text"
                label={"Estado"}
                bind:value={$form.state}
                error={$form.errors?.state}
            />
            <Input
                type="text"
                label={"Ciudad"}
                bind:value={$form.city}
                error={$form.errors?.city}
            />
            <Input
                type="textarea"
                label={"Dirección específica"}
                bind:value={$form.address}
                error={$form.errors?.address}
                classes="col-span-2"
            />
        </fieldset> -->
    </form>
    <button
        form="a-form"
        slot="btn_footer"
        type="submit"
        class="btn btn-green w-1/2 mr-7 flex items-center justify-center gap-3"
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
            <span> {submitStatus === "Crear" ? "Crear" : "Actualizar"} </span>
        {/if}
    </button>
</Modal>

<div class="flex justify-between items-center">
    <div class="w-44">
        <Input
            id="filterYear"
            type="select"
            value={selectedCourseId}
            on:change={(e) => {
                console.log("Cambiando año a:", e.target.value);
                changeYear(e.target.value);
            }}
        >
            {#each data.courses as course}
                <option class="bg-gray-50" value={course.id.toString()}
                    >{course.name}</option
                >
            {/each}
        </Input>
    </div>
    <button
        class="btn inline-block"
        on:click={(e) => {
            e.preventDefault();
            if (submitStatus === "Editar") {
                $form.reset();
                submitStatus = "Crear";
                editingStudentId = null;
                selectedRow = { status: false, id: 0 };
            } else {
                $form.section_id = +data.filters.section_id;
                $form.course_id = +data.filters.course_id;
            }

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
                class="btn-ghost px-4 py-2"
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
                on:click={() => {
                    if (selectedRow.status && selectedRow.data.id === row.id) {
                        selectedRow = { status: false, id: 0 };
                    } else {
                        selectedRow = { status: true, data: { ...row } };
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

<style>
    fieldset {
        background-color: #fffdf5;
        background-image: url("https://www.transparenttextures.com/patterns/rice-paper-2.png");
        /* This is mostly intended for prototyping; please download the pattern and re-host for production environments. Thank you! */
    }
</style>
