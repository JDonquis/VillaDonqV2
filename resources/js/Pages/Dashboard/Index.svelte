<!-- <script>
    import { useForm } from "@inertiajs/svelte";

    // import secretariaLogo from '$lib/images/logo_secretaria-circle-main.png';
    // import Input from "../../components/Input.svelte";
    import Modal from "../../components/Modal.svelte";

    import Alert from "../../components/Alert.svelte";
    import { displayAlert } from "../../stores/alertStore";
    let showModal = false;
    const data = useForm({
        name: "Maestro José Marti",
        active_students: "400",
        promotions: "33",
        years: "34",
        slogan: "Formando mentes brillantes para un mañana prometedor",
        courses: [1, 2, 3],
    });
    $: console.log(data);

    // function resizeInput(event) {
    //     event.target.style.width = event.target.value.length + "ch";
    // }
</script>

<Alert />
<section class="bg-background">
    <div class="py-5"></div>

    <h2>Configuración del perfil</h2>

    <form
        class="bg-background px-1 mx-4 md:py-9 md:grid justify-between grid-flow-col md:gap-x-10 lg:gap-x-24 items-center"
    >
        <div class="md:min-w-[600px] max-w-[690px]">
            <span class="md:text-5xl text-color1 font-bold">
                Colegio
                <br />
                <input
                    class="md:text-5xl bg-transparent"
                    type="text"
                    bind:value={$data.name}
                    style={`width:${$data.name.length - 3}ch`}
                />
            </span>
            <textarea
                class="block w-full bg-transparent md:text-xl"
                type="text"
                bind:value={$data.slogan}
            />

            <div class="flex justify-between mt-4 md:mt-14 text-color1">
                <div>
                    <label
                        class="flex items-center gap-2 mb-2 lg:mb-3 cursor-pointer"
                    >
                        <input
                            type="checkbox"
                            bind:group={$data.courses}
                            value={1}
                            class="hidden"
                        />

                        {#if $data.courses.includes(1)}
                            <div
                                class="bg-color1 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"
                            >
                                <iconify-icon
                                    class="text-color4 text-4xl"
                                    icon="pajamas:check-xs"
                                ></iconify-icon>
                            </div>
                            <b>Prescolar</b>
                        {:else}
                            <div
                                class="bg-gray-400 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"
                            >
                                <iconify-icon
                                    icon="octicon:no-entry-16"
                                    class="text-gray-300"
                                ></iconify-icon>
                            </div>
                            <b class="text-gray-400">Prescolar</b>
                        {/if}
                    </label>
                    <ul class="grid grid-cols-2 gap-x-3">
                        <li>1er nivel</li>
                        <li>2do nivel</li>
                        <li>3er nivel</li>
                    </ul>
                </div>
                <div>
                    <label
                        class="flex items-center gap-2 mb-2 lg:mb-3 cursor-pointer"
                    >
                        <input
                            type="checkbox"
                            bind:group={$data.courses}
                            value={2}
                            class="hidden"
                        />
                        {#if $data.courses.includes(2)}
                            <div
                                class="bg-color1 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"
                            >
                                <iconify-icon
                                    class="text-color4 text-4xl"
                                    icon="pajamas:check-xs"
                                ></iconify-icon>
                            </div>
                            <b>Primaria</b>
                        {:else}
                            <div
                                class="bg-gray-400 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"
                            >
                                <iconify-icon
                                    icon="octicon:no-entry-16"
                                    class="text-gray-300"
                                ></iconify-icon>
                            </div>
                            <b class="text-gray-400">Primaria</b>
                        {/if}
                    </label>
                    <ul class="grid grid-cols-2 gap-x-3">
                        <li>1er grado</li>
                        <li>2do grado</li>
                        <li>3er grado</li>
                        <li>4to grado</li>
                        <li>5to grado</li>
                        <li>6to grado</li>
                    </ul>
                </div>
                <div>
                    <label
                        class="flex items-center gap-2 mb-2 lg:mb-3 cursor-pointer"
                    >
                        <input
                            type="checkbox"
                            bind:group={$data.courses}
                            value={3}
                            class="hidden"
                        />

                        {#if $data.courses.includes(3)}
                            <div
                                class="bg-color1 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"
                            >
                                <iconify-icon
                                    class="text-color4 text-4xl"
                                    icon="pajamas:check-xs"
                                ></iconify-icon>
                            </div>
                            <b>Secundaria</b>
                        {:else}
                            <div
                                class="bg-gray-400 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"
                            >
                                <iconify-icon
                                    icon="octicon:no-entry-16"
                                    class="text-gray-300"
                                ></iconify-icon>
                            </div>
                            <b class="text-gray-400">Secundaria</b>
                        {/if}
                    </label>
                    <ul class="grid grid-cols-2 gap-x-3">
                        <li>1er año</li>
                        <li>2do año</li>
                        <li>3er año</li>
                        <li>4to año</li>
                        <li>5to año</li>
                    </ul>
                </div>
            </div>

            <div
                class="flex justify-between w-full mt-4 md:mt-16 md:gap-10 text-color1"
            >
                <div class="flex divide-x divide-dark">
                    <input
                        class="px-1 text-4xl bg-transparent"
                        bind:value={$data.years}
                        style={`width:${$data.years.length}ch`}
                    />
                    <p class="pl-3 col-span-2 leading-5 font-semibold">
                        AÑOS DE
                        <br />
                        FORMACIÓN
                    </p>
                </div>

                <div class="flex divide-x divide-dark">
                    <input
                        class="px-1 text-4xl bg-transparent"
                        bind:value={$data.promotions}
                        style={`width:${$data.promotions.length + 0.5}ch`}
                    />
                    <p class="pl-3 col-span-2 leading-5 font-semibold">
                        PROMOCIONES
                        <br />
                        GRADUADAS
                    </p>
                </div>

                <div class="flex divide-x divide-dark">
                    <input
                        class="px-1 text-4xl bg-transparent"
                        bind:value={$data.active_students}
                        style={`width:${$data.active_students.length + 0.5}ch`}
                    />
                    <p class="pl-3 col-span-2 leading-5 font-semibold">
                        ESTUDIANTES
                        <br />
                        ACTIVOS
                    </p>
                </div>
            </div>
        </div>

        <label
            class="pl-5 relative pr-2 max-w-[500px] flex items-center justify-center rounded-full big_picture_label cursor-pointer"
        >
            <img
                class="absolute w-full"
                src="https://cdn.discordapp.com/attachments/1238903237218930802/1244452251028688906/Iconos.png?ex=6655d2b9&is=66548139&hm=13ffaaa80051f10b14f4ac464ba1edc1a2b82a9546f069c85de0dfde2da6309a&"
                alt=""
            />

            
            <img
                class="rounded-full aspect-square border-4 object-cover border-color1 bg-blend-overlay hover:bg-blend-darken"
                src="http://127.0.0.1:8000/storage/institution/institution.jpeg"
                alt=""
            />

            <iconify-icon
                icon="line-md:edit"
                class="text-dark text-6xl bg-white bg-opacity-40 p-20 md:p-32 xl:p-48 hidden absolute rounded-full mx-auto"
            ></iconify-icon>
            <input type="file" name="" id="" class="hidden" />
        </label>
        {#if $data.isDirty}
            <button class="shadow-xl  fixed flex items-center right-20 px-10 py-4 rounded bottom-10 bg-color1 gap-3 text-color4"
                >GUARDAR 
                <iconify-icon icon="material-symbols:save" class="text-3xl"></iconify-icon>
                
                </button
            >
        {/if}
    </form>

    <div class="Configuracion_tarifas">
        <h2>Configuración de tarifas</h2>

        <div class="#">
            <label>
                <input
                type="number"
                required={true}
                class={"z-50 mx-auto p-2 mt-6 md:w-60 bg-color6 text-black border rounded-md"}>  
            </label>

            <label>
                <input
                type="number"
                required={true}
                class={"z-50 mx-auto p-2 mt-6 md:w-60 bg-color6 text-black border rounded-md"}>  
            </label>

            <label>
                <input
                type="number"
                required={true}
                class={"z-50 mx-auto p-2 mt-6 md:w-60 bg-color6 text-black border rounded-md"}>  
            </label>   
        </div>
    </div>

    <div class="#">
        <h2>Configuracion de metodos de pago</h2>

    </div>

</section>

<style>
    * {
        box-sizing: border-box;
    }
    textarea {
        resize: none;
    }
    .big_picture_label:hover iconify-icon {
        display: block;
    }
</style> -->
