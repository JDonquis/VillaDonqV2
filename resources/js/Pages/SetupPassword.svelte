<script>
    import { useForm, page } from "@inertiajs/svelte";
    import Input from "../components/Input.svelte";
    import Alert from "../components/Alert.svelte";
    import { displayAlert } from "../stores/alertStore";

    export let token;

    let form = useForm({
        token: token,
        password: null,
        password_confirmation: null,
    });

    function handleSubmit(event) {
        event.preventDefault();
        $form.clearErrors();
        $form.post("/establecer-contrasena", {
            onSuccess: () => {
                displayAlert({ type: "success", message: "Contraseña establecida exitosamente" });
                setTimeout(() => {
                    window.location.href = "/";
                }, 2000);
            },
            onError: (errors) => {
                if (errors.message) {
                    displayAlert({ type: "error", message: errors.message });
                }
            },
        });
    }
</script>

<Alert />
<section class="bg-background min-h-screen flex items-center justify-center">
    <div class="bg-color1 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-white text-center mb-6">Establecer Contraseña</h1>
        <p class="text-gray-300 text-center mb-6">Ingresa tu nueva contraseña para acceder al sistema.</p>
        
        <form on:submit={handleSubmit} class="space-y-4">
            <input type="hidden" bind:value={$form.token} />
            
            <div>
                <Input
                    type="password"
                    name="password"
                    label="Nueva Contraseña"
                    required={true}
                    bind:value={$form.password}
                    error={$form.errors?.password}
                />
            </div>
            
            <div>
                <Input
                    type="password"
                    name="password_confirmation"
                    label="Confirmar Contraseña"
                    required={true}
                    bind:value={$form.password_confirmation}
                    error={$form.errors?.password_confirmation}
                />
            </div>
            
            <button
                type="submit"
                class="btn_create w-full mt-4"
                disabled={$form.processing}
            >
                {$form.processing ? 'Guardando...' : 'Establecer Contraseña'}
            </button>
        </form>
    </div>
</section>