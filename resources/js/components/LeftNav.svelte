<script>
    // import { page } from "$app/stores";

    // $: url = $page.route.id
    // console.log(url)
    import {toggleMenu} from "../stores/navStatus.js"
    import { inertia, page } from "@inertiajs/svelte";

    const navPages = [
        {
            icon: "uil:setting",
            href: "/dashboard/configuracion",
            name: "Configuración",
        },
        {
            icon: "mdi:google-classroom",
            href: "/dashboard/matricula",
            name: "Matricula",
        },
    
        {
            icon: "streamline:payment-10",
            href: "/dashboard/pagos",
            name: "Pagos",
        },
        {
            icon: "streamline:payment-10",
            href: "/dashboard/registrar-pago",
            name: "Registrar pago",
        },
    ];
    console.log($page);
</script>

<nav class="left_nav flex md:block items-center bg-color1  text-gray-200 h-full relative rounded-3xl overflow-hidden">
    <button on:click={() => toggleMenu()} class="hidden md:block burger_icon  items-center pt-1  text-center text-2xl text-gray-300 hover:text-color4 bg-gray-700 bg-opacity-50 ">
    
        <iconify-icon class="mb-0 pb-0" icon="majesticons:menu-expand-left-line"></iconify-icon>
    </button>
    <a href="" class="logo text-lg hidden md:block p-4 text-center w-full inline-block">VILLADONQ</a>
    <ul
        class="flex justify-around items-center w-full md:flex-col
    md:items-start md:justify-normal md:p-2 md:gap-1 md:[&>*]:w-full"
    >
        {#each navPages as navPage}
            <li>
                
                <a
                    href={navPage.href}
                    use:inertia
                    class="hover:text-color4 z-10 rounded-md flex md:gap-2 items-center p-2"
                    class:active={$page.url.startsWith(navPage.href)}
                    ><iconify-icon
                        class="text-xlc md:text-lg"
                        icon={navPage.icon}
                    /><span class="label_link hidden md:block"
                        >{navPage.name}</span
                    ></a
                >
            </li>
        {/each}
    </ul>
        

</nav>

<style>
    .active {
        /* background: linear-gradient(80deg, #acd7f2 9%, transparent 99%); */
        position: relative;
        border-radius: 6px;
        /* box-shadow: 0 0 10px 0px #9BF2EA; */
        color: #21e6c1 !important;
        font-weight: bold;
    }
    a:before {

    }
    a.active:before {
        
    }
    a:active {
    }
</style>
