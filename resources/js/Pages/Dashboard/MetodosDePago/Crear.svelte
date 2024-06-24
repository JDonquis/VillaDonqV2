<script>
    import Input from "../../../components/Input.svelte";
    import { useForm, router } from "@inertiajs/svelte";
    import ColorsPayMethods from "../../../components/ColorsPayMethods"
    import Alert from "../../../components/Alert.svelte";
    import { displayAlert } from "../../../stores/alertStore";

    export let data;
    let objFileds = {};
    data?.fields?.forEach((field) => {
        objFileds[field] = "";
    });

    let formCreate = useForm({
        ...objFileds,
         payment_method_id: data.method.id
    });
    function handleSubmit(event) {
        console.log('enviando')
        event.preventDefault();
        $formCreate.clearErrors();
        $formCreate.post("/dashboard/configuracion/crear-cuenta", {
            onError: (errors) => {
                if (errors.data) {
                    displayAlert({ type: "error", message: errors.data });
                }
            },
            onSuccess: (mensaje) => {
                $formCreate.reset();
                displayAlert({
                    type: "success",
                    message: `${data.method.name} añadido correctamente`,
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
            Nuevo método: <b>{data.method.name}</b>
        </h2>

    </header>
    <div class="p-10 pt-5">
        {#if objFileds.hasOwnProperty("bank")}
            <Input
                type="text"
                required={true}
                label={"Banco"}
                bind:value={$formCreate.bank}
                error={$formCreate.errors?.bank}
            />
        {/if}
        {#if objFileds.hasOwnProperty("account_number")}
            <Input
                type="text"
                required={true}
                label={"Nro de cuenta Bancaria"}
                bind:value={$formCreate.account_number}
                error={$formCreate.errors?.account_number}
            />
        {/if}
        {#if objFileds.hasOwnProperty("phone_number")}
            <Input
                type="text"
                required={true}
                label={"Teléfono"}
                bind:value={$formCreate.phone_number}
                error={$formCreate.errors?.phone_number}
            />
        {/if}
        {#if objFileds.hasOwnProperty("ci")}
            <Input
                type="text"
                required={true}
                label={"Cédula"}
                bind:value={$formCreate.ci}
                error={$formCreate.errors?.ci}
            />
        {/if}
        {#if objFileds.hasOwnProperty("person_name")}
            <Input
                type="text"
                required={true}
                label={"Persona titular"}
                bind:value={$formCreate.person_name}
                error={$formCreate.errors?.person_name}
            />
        {/if}

        {#if objFileds.hasOwnProperty("username")}
            <Input
                type="text"
                required={true}
                label={"Nombre de usuario"}
                bind:value={$formCreate.username}
                error={$formCreate.errors?.username}
            />
        {/if}
        {#if objFileds.hasOwnProperty("email")}
            <Input
                type="text"
                required={true}
                label={"Correo"}
                bind:value={$formCreate.email}
                error={$formCreate.errors?.email}
            />
        {/if}
        <input
            form="a-form"
            type="submit"
            value={$formCreate.processing ? "Cargando..." : "Guardar"}
            class="hover:bg-color3 hover:text-white duration-200 w-full mt-5 bg-color4 text-black font-bold py-3 rounded-md cursor-pointer"
        />
    </div>
</form>
