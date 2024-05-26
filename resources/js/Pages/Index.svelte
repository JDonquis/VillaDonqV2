<script>
    import { useForm } from "@inertiajs/svelte";

    // import secretariaLogo from '$lib/images/logo_secretaria-circle-main.png';
    import Input from "../components/Input.svelte";
    import Alert from "../components/Alert.svelte";
    import { displayAlert } from "../stores/alertStore";

    let form = useForm({
        ci: null,
        password: null,
    });

    function handleSubmit(event) {
        event.preventDefault();
        $form.clearErrors()
        $form.post("/login",  {
            onError: (errors) => {
                if(errors.data) {
                    displayAlert({type: "error", message: errors.data})
                }
            },
        });
    }
    
</script>

<Alert />
<main class="bg-background h-screen">
    <div class="flex flex-col gap-3 items-center justify-center mb-1 py-6">
        <!-- <img src={secretariaLogo} class="max-w-12 lg:max-w-16" alt="" /> -->
        <h1 class=" text-4xl tracking-widest">Villadonq</h1>
    </div>
    <div class="flex justify-center items-center w-full">
        <!-- <img src={doctor} class="max-w-96 h-96" alt="" /> -->

        <div class="text-center text-gray-300">
            <form
                on:submit={handleSubmit}
                class="mx-auto mt-10 bg-white bg-opacity-5 rounded-lg shadow-2xl p-6 w-[300px] text-center flex flex-col md:h-96 min-h-96"
            >
                <legend class=" opacity-70 mb-6">INICIAR SESIÓN</legend>
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
                    class="hover:bg-color3 hover:text-white duration-200 mt-auto w-full bg-color2 text-black font-bold py-3 rounded-md cursor-pointer"
                />
            </form>
        </div>
        <!-- <img src={ladyDoctor} class="max-w-96 h-96" alt="" /> -->
    </div>
</main>

<style>
    /* main {} */
</style>
