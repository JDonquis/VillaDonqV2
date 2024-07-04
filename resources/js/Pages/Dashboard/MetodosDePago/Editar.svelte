<script>
    import Input from "../../../components/Input.svelte";
    import { useForm, router } from "@inertiajs/svelte";
    import ColorsPayMethods from "../../../components/ColorsPayMethods"
    import Alert from "../../../components/Alert.svelte";
    import { displayAlert } from "../../../stores/alertStore";

    export let data;
   console.log(data.account)

    let formData = useForm({
        ...data.account.data,
        
    });
    function handleSubmit(event) {
        console.log('enviando')
        event.preventDefault();
        $formData.clearErrors();
        $formData.put(`/dashboard/configuracion/editar-cuenta/${$formData.id}`, {
            preserveScroll: false,
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
            onSuccess: (mensaje) => {
                $formData.reset();
                displayAlert({
                    type: "success",
                    message: `${data.method.name} actualizado correctamente`,
                });
                showModal = false;
            },
        });
    }
</script>

<form
    action=""
    id="a-form" 
    on:submit={handleSubmit}
    class="bg-color1 p max-w-[450px] mx-auto rounded-md text-gray-50"
>
    <header class={`bg-gray-100 text-dark py-4 pl-3`}>
        <h2 class={`border-l-4 border-${ColorsPayMethods()[data.method.name]} inline pl-3`}>
            Editar método: <b>{data.method.name}</b>
        </h2>

    </header>
    <div class="p-10 pt-5">
        {#if data.account.data.hasOwnProperty("bank")}
            <Input
                type="text"
                required={true}
                label={"Banco"}
                bind:value={$formData.bank}
                error={$formData.errors?.bank}
            />
        {/if}
        {#if data.account.data.hasOwnProperty("account_number")}
            <Input
                type="text"
                required={true}
                label={"Nro de cuenta Bancaria"}
                bind:value={$formData.account_number}
                error={$formData.errors?.account_number}
            />
        {/if}
        {#if data.account.data.hasOwnProperty("phone_number")}
            <Input
                type="text"
                required={true}
                label={"Teléfono"}
                bind:value={$formData.phone_number}
                error={$formData.errors?.phone_number}
            />
        {/if}
        {#if data.account.data.hasOwnProperty("ci")}
            <Input
                type="text"
                required={true}
                label={"Cédula"}
                bind:value={$formData.ci}
                error={$formData.errors?.ci}
            />
        {/if}
        {#if data.account.data.hasOwnProperty("person_name")}
            <Input
                type="text"
                required={true}
                label={"Persona titular"}
                bind:value={$formData.person_name}
                error={$formData.errors?.person_name}
            />
        {/if}

        {#if data.account.data.hasOwnProperty("username")}
            <Input
                type="text"
                required={true}
                label={"Nombre de usuario"}
                bind:value={$formData.username}
                error={$formData.errors?.username}
            />
        {/if}
        {#if data.account.data.hasOwnProperty("email")}
            <Input
                type="text"
                required={true}
                label={"Correo"}
                bind:value={$formData.email}
                error={$formData.errors?.email}
            />
        {/if}
        <input
            form="a-form"
            type="submit"
            value={$formData.processing ? "Cargando..." : "Guardar"}
            class="hover:bg-color3 hover:text-white duration-200 w-full mt-5 bg-color4 text-black font-bold py-3 rounded-md cursor-pointer"
        />
    </div>
</form>
