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

    setTimeout(() => {
        document.querySelector("input[name='ci']").focus();
    }, 200);
</script>

<Alert />

<div
    class="relative min-h-screen w-full overflow-hidden bg-[#490A75] flex items-center justify-center font-sans select-none"
>
    <div class="absolute inset-0 w-full h-full pointer-events-none">
        <div
            class="absolute -top-[25%] left-[14%] md:left-[40%] w-[120px] h-[960px] bg-white rotate-[60deg] transform origin-top"
        ></div>
        <div
            class="absolute -top-[17%] left-[25%] md:left-[2%] w-[3000px] h-[120px] bg-white rotate-[60deg] transform origin-left"
        ></div>

        <div
            class="absolute -bottom-20 left-0 w-[2400px] h-[25vh] md:h-[30vh] -rotate-6 bg-[#008f11] clip-diagonal shadow-inner"
        ></div>
    </div>

    <div
        class="relative z-10 container mx-auto px-6 flex flex-col lg:flex-row items-center justify-end gap-12 lg:gap-24 w-full"
    >
        <div
            class="flex flex-col items-center justify-center text-center animate-fade-in"
        >
            <div
                class="w-64 md:w-96 rounded-full overflow-hidden p-1 transition-transform duration-300 hover:scale-105"
            >
                <img
                    src="/img/colegio_logo.png"
                    alt="U.E. Instituto Jesús El Nazareno"
                    class="w-full h-full object-contain"
                />
            </div>
        </div>

        <div
            class="w-full max-w-sm bg-[#490A75]/40 backdrop-blur-md p-8 rounded-2xl border border-white/10 shadow-2xl animate-slide-up"
        >
            <h2
                class="text-white text-xl font-bold text-center mb-6 tracking-wide uppercase"
            >
                <img
                    src="/img/Isotipo-villadonq-blanco.png"
                    alt="Villadonq"
                    class="w-10 h-10 object-contain inline"
                />
            </h2>
            <form on:submit={handleSubmit} class="min-w-[270px] px-5">
                <div>
                    <Input
                        type="text"
                        labelClass="text-white"
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
                        labelClass="text-white"
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
                    class="btn w-full mt-6"
                />

                <div class="mt-4 text-center">
                    <a
                        href="/olvidar-contrasena"
                        class="text-sm text-gray-400 hover:text-color2"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    * {
        box-sizing: border-box;
    }
</style>
