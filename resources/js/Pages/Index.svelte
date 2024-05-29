<script>
    import { useForm } from "@inertiajs/svelte";

    // import secretariaLogo from '$lib/images/logo_secretaria-circle-main.png';
    import Input from "../components/Input.svelte";
    import Modal from "../components/Modal.svelte";

    import Alert from "../components/Alert.svelte";
    import { displayAlert } from "../stores/alertStore";
    let showModal = false;

    let form = useForm({
        ci: null,
        password: null,
    });

    function handleSubmit(event) {
        event.preventDefault();
        $form.clearErrors();
        $form.post("/login", {
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
        });
    }
</script>

<Alert />
<section class="bg-background min-h-screen">
    <header class="flex justify-between px-1 mx-4 md:mx-12 py-3 lg:mx-20">
        <img src="file:///C:/Users/Juan/Downloads/logo_colegio_negro%20(1).png" alt="">
        <button
            class="btn_create inline-block"
            on:click={(e) => {
                e.preventDefault();
                showModal = true;
            }}>Login</button
        >
    </header>
    <Modal bind:showModal>
        <legend slot="header" class="text-center opacity-70"
            >INICIAR SESIÓN</legend
        >
        <form on:submit={handleSubmit} class="min-w-[250px]">
            <div>
                <Input
                    type="text"
                    name="ci"
                    required={true}
                    label={"Cédula"}
                    bind:value={$form.ci}
                    error={$form.errors?.ci}
                />
                <!-- {#if $form.errors.ci}
            <div class="text-white bg-opacity-30 bg-red pt-1">
                
                <span >{$form.errors.ci}</span>
            </div>
            {/if} -->

                <Input
                    type="password"
                    required={true}
                    name="password"
                    label={"Contraseña"}
                    bind:value={$form.password}
                />
            </div>
            <!-- <button type="submit">Iniciar sesión</button> -->

            <input
                type="submit"
                disabled={$form.processing}
                value={$form.processing ? "Cargando..." : "ENTRAR"}
                class="hover:bg-color3 hover:text-white duration-200 mt-5 w-full bg-color4 text-black font-bold py-3 rounded-md cursor-pointer"
            />
        </form>
    </Modal>

    <main
        class="bg-background px-1 mx-4 md:py-9 md:mx-12 lg:mx-20 justify-between md:grid grid-flow-col md:gap-x-10 lg:gap-x-24 xl:gap-32 items-center"
    >
        <div class="md:min-w-[600px]">
            <h1 class="md:text-5xl text-color1">
                Colegio
                <br />
                Maestro José Marti
            </h1>
            <h2 class="text-xl">
                Formando mentes brillantes para un mañana prometedor
            </h2>

            <div class="flex justify-between mt-4 md:mt-14 text-color1">
                <div>
                    <span class="flex items-center gap-2 mb-2 lg:mb-3">
                        <div class="bg-color1 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"><iconify-icon class="text-color4 text-4xl" icon="pajamas:check-xs"></iconify-icon></div>
                        <b>Prescolar</b>
                    </span>
                    <ul class="grid grid-cols-2 gap-x-3">
                        <li>1er nivel</li>
                        <li>2do nivel</li>
                        <li>3er nivel</li>
                    </ul>
                </div>
                <div>
                    <span class="flex items-center gap-2 mb-2 lg:mb-3">
                        <div class="bg-color1 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"><iconify-icon class="text-color4 text-4xl" icon="pajamas:check-xs"></iconify-icon></div>
                        <b>Primaria</b>
                    </span>
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
                    <span class="flex items-center gap-2 mb-2 lg:mb-3">
                        <div class="bg-color1 w-6 md:w-8 aspect-square rounded-full overflow-hidden flex items-center justify-center"><iconify-icon class="text-color4 text-4xl" icon="pajamas:check-xs"></iconify-icon></div>
                        <b>Secundaria</b>
                    </span>
                    <ul class="grid grid-cols-2 gap-x-3">
                        <li>1er año</li>
                        <li>2do año</li>
                        <li>3er año</li>
                        <li>4to año</li>
                        <li>5to año</li>
                    </ul>
                </div>
            </div>


            <div class="flex justify-between  mt-4 md:mt-16 md:gap-10  text-color1">

                <div class="flex divide-x divide-dark ">
                    <span class="pr-3 text-4xl">33</span>
                    <p class="pl-3 col-span-2 leading-5 font-semibold">
                        AÑOS DE
                        <br>
                        FORMACIÓN
                    </p>
                </div>

                <div class="flex divide-x divide-dark ">
                    <span class="pr-3 text-4xl">32</span>
                    <p class="pl-3 col-span-2 leading-5 font-semibold">
                        PROMOCIONES 
                        <br>
                        GRADUADAS
                    </p>
                </div>

                <div class="flex divide-x divide-dark ">
                    <span class="pr-3 text-4xl">400</span>
                    <p class="pl-3 col-span-2 leading-5 font-semibold">
                        ESTUDIANTES
                        <br>
                        ACTIVOS
                    </p>
                </div>
            </div>



        </div>

        <div class="pl-5 relative pr-2 max-w-[500px]">
            <img class="absolute w-full" src="https://cdn.discordapp.com/attachments/1238903237218930802/1244452251028688906/Iconos.png?ex=6655d2b9&is=66548139&hm=13ffaaa80051f10b14f4ac464ba1edc1a2b82a9546f069c85de0dfde2da6309a&" alt="" >
            <img    
                class="rounded-full aspect-square border-4 object-cover border-color1   "
                src="https://notifalcon.com/wp-content/uploads/2023/11/WhatsApp-Image-2023-11-08-at-10.21.36-AM.jpeg"
                alt=""
            />
        </div>
    </main>
</section>

<style>
    * {
        box-sizing: border-box;
    }
</style>
