<script>
    import { onMount } from "svelte";
    import LeftNav from "./LeftNav.svelte";
    import Header from "./Header.svelte";
    import Footer from "./Footer.svelte";
	import 'iconify-icon';
	import { navStatus } from '../stores/navStatus';
    import { element } from "svelte/internal";
    let screenWidth=  window.innerWidth 
    // $: mainWidth = main.offsetWidth;
    let mainWidth = `width:${screenWidth}%`
    $: if (screenWidth > 768) {
        mainWidth =`width:${screenWidth - $navStatus.navWidth-20}px`
    }
    
</script>

<svelte:head>

</svelte:head>
<div class="bg-gray-700 h-screen">

    <section class="bg-gray-200 h-full  dashboard_container p-2 rounded-2xl" class:menuStatus-false={$navStatus.isContracted}>
        
        <Header />
        <div class=" main_and_footer_container  " bing:this="main_and_footer_container" scroll-region>
            <main  style={mainWidth}  class={`mx-auto  main_dashboard   relative px-10 duration-100 pb-10 pt-3`} >
                <slot />
            </main>
            <Footer />
    
        </div>
        <LeftNav />
    </section>
</div>
