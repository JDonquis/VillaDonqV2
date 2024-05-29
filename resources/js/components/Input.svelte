<!-- FloatingInput.svelte -->
<script>
    export let value = "";
    export let label = "";
    export let required = "";
    export let placeholder = "";
    export let type = "text";
    export let classes = ''
    export let theme = "ligtht"
    export let min = ""
    export let max = ""

    export let error = false;
</script>

<div class={`text-left w-full mt-5 ${theme == "dark" ? "bg-color1 text-gray-100" : ''}`}>
    <label for={label} class={`form__label w-full text-gray-300 ${theme == "dark" ? "bg-color1 text-gray-100" : ''}`} {placeholder}>{label} {required? "*" : "" }</label>
    <div class="relative w-full parent_div">
        {#if type === "textarea"}
            <textarea bind:value id={label} rows="1"></textarea>
        {:else if type === "select"}
                
            <select id={label} bind:value  required={required}>
                <slot></slot>
            </select>
        {:else}
            <input
                bind:value
                {...{ type }}
                id={label}
                class="form__field"
                required={required}
                max={max}
                min={min}
                on:input
            />
        {/if}
        {#if error}
            <div class="text-white bg-opacity-30 bg-red pt-1 px-2">
                <span>{error}</span>
            </div>
        {/if}
    </div>
</div>

<!-- <input  class="bg-color1 block px-2.5 pb-2.5 pt-4 w-full text-sm   rounded-lg  appearance-none dark:text-white  focus:outline-none focus:ring-0  peer " placeholder=" "   />
    <label  class="absolute text-sm   duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] rounded-xl px-2 peer-focus:px-2 text-white peer-focus:text-color2 peer-focus:dark:text-color2  peer-placeholder-shown:text-color2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-4 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1 ">{label}</label> -->

<style>
    input,
    textarea,
    select {
        width: 100%;
        padding: 10px;
        padding-block: 8px;
        border: 0;
        border-radius: 5px;
        background: hwb(217 91% 0% / 0.144);
        border-bottom: 1px solid gray;
    }
    select {
        padding: 11px;
    }
    option {
        background: #000;
    }
    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
    }
    input:-webkit-autofill,
    textarea:-webkit-autofill,
    select:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 50px #3b414d inset; /* Change the color to your own background color */
        -webkit-text-fill-color: gainsboro;
        font-family: "Figtree";
    }

    input:-webkit-autofill:focus,
    textarea:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 50px #3b414d inset; /*your box-shadow*/
        -webkit-text-fill-color: gainsboro;
        font-family: "Figtree";
    }
    .parent_div:before {
        content: "";
        display: block;
        position: absolute;
        transition: 0.3s cubic-bezier(0.39, 0.575, 0.565, 1);
        bottom: 1px;
        left: 2.5px;
        border-radius: 4px;
        height: 2px;
        background: linear-gradient(80deg, #54ffaf 9%, #1f4287 93%);
        background-color: aqua;
        width: 0%;
    }
    .parent_div:focus-within::before {
        width: 98%;
    }
   
</style>
