<script>
    import { useForm } from "@inertiajs/svelte";
    import Input from "../components/Input.svelte";
    import Alert from "../components/Alert.svelte";
    import { displayAlert } from "../stores/alertStore";

    export let sent = false;
    export let errors = {};

    let form = useForm({
        ci: null,
    });

    function handleSubmit(event) {
        event.preventDefault();
        $form.clearErrors();
        $form.post("/olvidar-contrasena", {
            onSuccess: () => {
                sent = true;
            },
            onError: (err) => {
                if (err.ci) {
                    displayAlert({ type: "error", message: err.ci });
                }
            },
        });
    }

    function goBack() {
        window.location.href = "/";
    }
</script>

<Alert />
<section class="bg-background min-h-screen flex items-center justify-center">
    <div class="bg-color1 p-8 rounded-lg shadow-lg w-full max-w-md">
        {#if sent}
            <div class="text-center">
                <h1 class="text-2xl font-bold text-white mb-4">Correo Enviado</h1>
                <p class="text-gray-300 mb-6">
                    Si el correo electrónico está registrado en nuestro sistema, recibirás un mensaje con instrucciones para restablecer tu contraseña.
                </p>
                <p class="text-gray-400 text-sm mb-6">
                    No compartas este enlace con nadie.
                </p>
                <button on:click={goBack} class="btn_create w-full">
                    Volver al Login
                </button>
            </div>
        {:else}
            <h1 class="text-2xl font-bold text-white text-center mb-2">¿Olvidaste tu Contraseña?</h1>
            <p class="text-gray-300 text-center mb-6">
                Ingresa tu número de Cédula de Identidad y te enviaremos un enlace para restablecer tu contraseña.
            </p>
            
            <form on:submit={handleSubmit} class="space-y-4">
                <div>
                    <Input
                        type="text"
                        name="ci"
                        label="Cédula de Identidad"
                        required={true}
                        bind:value={$form.ci}
                        error={$form.errors?.ci}
                    />
                </div>
                
                <button
                    type="submit"
                    class="btn_create w-full mt-4"
                    disabled={$form.processing}
                >
                    {$form.processing ? 'Enviando...' : 'Enviar Enlace de Recuperación'}
                </button>

                <button
                    type="button"
                    on:click={goBack}
                    class="text-gray-400 hover:text-white text-sm w-full mt-2"
                >
                    ← Volver al Login
                </button>
            </form>
        {/if}
    </div>
</section>