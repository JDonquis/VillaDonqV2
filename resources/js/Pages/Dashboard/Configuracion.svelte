<script>
    import { useForm } from "@inertiajs/svelte";
    import clickOutside from "../../components/ClickOutside";
    import { inertia, router } from "@inertiajs/svelte";
    import ColorsPayMethods from "../../components/ColorsPayMethods";

    import Alert from "../../components/Alert.svelte";
    import { displayAlert } from "../../stores/alertStore";
    export let data;
    console.log({ data });

    const institution = useForm({
        name: "Maestro José Marti",
        active_students: "400",
        promotions: "33",
        years: "34",
        slogan: "Formando mentes brillantes para un mañana prometedor",
        courses: [1, 2, 3],
    });
    // function resizeInput(event) {
    //     event.target.style.width = event.target.value.length + "ch";
    // }

    const prices = useForm({
        ...data.prices
    });

    function updatePrices(e) {
        e.preventDefault()
        $prices.put(`/dashboard/configuracion/pagos`, {
            onBefore: () => confirm("¿Está seguro de guardar estos cambios?"),
            onSuccess: (mensaje) => {
                $prices.reset()
                displayAlert({
                    type: "success",
                    message: "Precios actualizados",
                });
               
            },
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
        });
    }


    function deleteAccount(id) {
        router.delete(`/dashboard/configuracion/eliminar-cuenta/${id}`, {
            onBefore: () => confirm("¿Está seguro de eliminar este metodo de pago?"),
            onSuccess: (mensaje) => {
                
                displayAlert({
                    type: "success",
                    message: "Método de pago eliminado",
                });
               
            },
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
        });
    }

    let showPaymentOptions = false;
</script>

<Alert />

<section class="bg-background">
    <div class="py-5"></div>

    <h2 class="font-bold text-xl">Configuración del perfil</h2>
    
    <form
        class="bg-background px-1 mx-4 md:py-9 md:pb-12 md:grid justify-between grid-flow-col md:gap-x-10 lg:gap-x-24 items-center relative"
    >
        <div class="md:min-w-[600px] max-w-[690px]">
            <span class="md:text-5xl text-color1 font-bold">
                Colegio
                <br />
                <input
                    class="md:text-5xl bg-transparent"
                    type="text"
                    bind:value={$institution.name}
                    style={`width:${$institution.name.length - 3}ch`}
                />
            </span>
            <textarea
                class="block w-full bg-transparent md:text-xl"
                type="text"
                bind:value={$institution.slogan}
            />

            <div class="flex justify-between mt-4 md:mt-14 text-color1">
                <div>
                    <label
                        class="flex items-center gap-2 mb-2 lg:mb-3 cursor-pointer"
                    >
                        <input
                            type="checkbox"
                            bind:group={$institution.courses}
                            value={1}
                            class="hidden"
                        />

                        {#if $institution.courses.includes(1)}
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
                            bind:group={$institution.courses}
                            value={2}
                            class="hidden"
                        />
                        {#if $institution.courses.includes(2)}
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
                            bind:group={$institution.courses}
                            value={3}
                            class="hidden"
                        />

                        {#if $institution.courses.includes(3)}
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
                        bind:value={$institution.years}
                        style={`width:${$institution.years.length}ch`}
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
                        bind:value={$institution.promotions}
                        style={`width:${$institution.promotions.length + 0.5}ch`}
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
                        bind:value={$institution.active_students}
                        style={`width:${$institution.active_students.length + 0.5}ch`}
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
    </form>
    {#if $institution.isDirty}
        <button
            class="shadow-xl slideIn flex items-center justify-center mb-3 ml-auto py-4 rounded w-64 bg-color1 gap-3 text-color4"
        >
            <span> GUARDAR PERFIL </span>
            <iconify-icon icon="material-symbols:save" class="text-3xl"
            ></iconify-icon>
        </button>
    {/if}

    <hr class=" border-gray-300" />

    <form class="Configuracion_tarifas my-10 py-3" id="pricesForm" on:submit={updatePrices}>
        <h2 class="font-bold text-xl mb-4">Configuración de tarifas</h2>

        <div class="flex gap-4 pl-4">
            <label class="flex flex-col">
                <span> Mensualidad ($): </span>
                <input
                    type="number"
                    required={true}
                    bind:value={$prices.monthly_payment}
                    class={"z-50  p-2 mt-1 md:w-60 bg-color6 text-black border rounded-md"}
                />
            </label>

            <label class="flex flex-col">
                <span> Inscripción nuevo ingreso ($): </span>
                <input
                    type="number"
                    required={true}
                    bind:value={$prices.new_inscription_price}
                    class={"z-50  p-2 mt-1 md:w-60 bg-color6 text-black border rounded-md"}
                />
            </label>

            <label class="flex flex-col">
                <span> Inscripción de regulares ($): </span>
                <input
                    type="number"
                    bind:value={$prices.regular_inscription_price}
                    required={true}
                    class={"z-50  p-2 mt-1 md:w-60 bg-color6 text-black border rounded-md"}
                />
            </label>
        </div>
    </form>
    {#if $prices.isDirty}

    <button
        class="shadow-xl slideIn flex items-center justify-center mb-3 ml-auto py-4 rounded w-64 bg-color1 gap-3 text-color4"
        type="submit"
        form={"pricesForm"}
    >
        <span> GUARDAR TARIFAS </span>
        <iconify-icon icon="material-symbols:save" class="text-3xl"
        ></iconify-icon>
    </button>
    {/if}
    <hr class=" border-gray-300" />
    <section class="my-10">
        <header class="flex justify-between mb-6">
            <h2 class="font-bold text-xl mb-4">
                Configuración de metodos de pago
            </h2>
            <div class="relative z-30">
                <button
                    on:click={() => (showPaymentOptions = !showPaymentOptions)}
                    class="btn_create gap-3 flex items-center"
                    use:clickOutside={() => {
                        showPaymentOptions = false;
                    }}
                >
                    <span> Nuevo Método </span>
                    <iconify-icon icon="line-md:plus"></iconify-icon>
                    <iconify-icon icon="mingcute:down-line"></iconify-icon>
                </button>
                {#if showPaymentOptions}
                    <div
                        class="payment_options slideIn absolute top-12 w-full bg-gray-100 text-dark shadow-xl p-1 rounded"
                    >
                        <ul class="flex flex-col gap-1">
                            {#each data.methods as method}
                                <li>
                                    <a
                                        class={`hover:bg-${ColorsPayMethods()[method.name]} hover:font-bold hover:text-gray-100 duration-100  border-l-4 border-${ColorsPayMethods()[method.name]} `}
                                        use:inertia
                                        href={`/dashboard/configuracion/crear-cuenta/${method.id}`}
                                    >
                                        {method.name}</a
                                    >
                                </li>
                            {/each}
                            <!-- <li>
                                <a
                                    class={` hover:bg-binance hover:font-bold hover:text-gray-100 duration-100  border-l-4 border-${ColorsPayMethods()[method.name]} `}
                                    use:inertia
                                    href={`/dashboard/configuracion/crear-cuenta/${method.id}`}
                                >
                                    {method.name}</a
                                >
                            </li> -->
                        </ul>
                    </div>
                {/if}
            </div>
        </header>
        <div class="flex flex-col gap-4">
            {#each data.accounts.data as payMethod}
                <article
                    id={`account-${payMethod.id}`}
                    class={`rounded-md bg-white border-l-8 border-${ColorsPayMethods()[payMethod.payment_method_name]} pb-5 pt-3 md:px-8`}
                >
                    <header class="flex justify-between">
                        <h3 class="text-color1 font-semibold">
                            {payMethod.payment_method_name}
                        </h3>
                        {#if payMethod.payment_method_name != "Efectivo"}
                            <div class="butons flex gap-3 text-gray-600">
                                <a
                                    href={`/dashboard/configuracion/editar-cuenta/${payMethod.id}`}
                                    class="hover:bg-color3 bg-opacity-10 hover:bg-opacity-20 cursor-pointer text-xl rounded border hover:border-color3 px-4 py-1"
                                    title="Editar"
                                >
                                    <iconify-icon
                                        class="relative -bottom-1"
                                        icon="ic:outline-edit"
                                    ></iconify-icon>
                                </a>

                                <button
                                    on:click={() => deleteAccount(payMethod.id)}
                                    class="hover:bg-red bg-opacity-10 hover:bg-opacity-20 cursor-pointer text-xl rounded border hover:border-red px-4 py-1"
                                    title="Eliminar"
                                >
                                    <iconify-icon
                                        class="text-xl relative top-1"
                                        icon="ph:trash"
                                    ></iconify-icon>
                                </button>
                            </div>
                        {/if}
                    </header>
                    <div
                        class="grid grid-cols-3 justify-items-start gap-4 py-2"
                    >
                        {#if payMethod?.bank}
                            <div>
                                <h4 class="text-gray-500">Banco:</h4>
                                <p>{payMethod.bank}</p>
                            </div>
                        {/if}
                        {#if payMethod?.phone_number}
                            <div>
                                <h4 class="text-gray-500">Teléfono:</h4>
                                <p>{payMethod.phone_number}</p>
                            </div>
                        {/if}
                        {#if payMethod?.ci}
                            <div>
                                <h4 class="text-gray-500">Cédula:</h4>
                                <p>{payMethod.ci}</p>
                            </div>
                        {/if}
                        {#if payMethod?.person_name}
                            <div>
                                <h4 class="text-gray-500">Titular:</h4>
                                <p>{payMethod.person_name}</p>
                            </div>
                        {/if}
                        {#if payMethod?.account_number}
                            <div>
                                <h4 class="text-gray-500">N° de cuenta:</h4>
                                <p>{payMethod.account_number}</p>
                            </div>
                        {/if}
                        {#if payMethod?.email}
                            <div>
                                <h4 class="text-gray-500">Correo:</h4>
                                <p>{payMethod.email}</p>
                            </div>
                        {/if}
                        {#if payMethod?.username}
                            <div>
                                <h4 class="text-gray-500">
                                    Nombre de usuario:
                                </h4>
                                <p>{payMethod.username}</p>
                            </div>
                        {/if}
                    </div>
                </article>
            {/each}
        </div>
    </section>
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
    .payment_options ul a {
        width: 100%;
        padding: 5px 10px;
        display: inline-block;
        /* background: red; */
    }
    .slideIn {
        animation-duration: 0.2s;
        animation-fill-mode: forwards;
        animation-name: slideIn;
    }
    @keyframes slideIn {
        0% {
            transform: translateY(-20px);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
