<script>
    import { alertInfo } from "../stores/alertStore";
    const clasesByTypeAlert = {
        success: "bg-green",
        info: "bg-white",
        error: "bg-red",
    };
</script>

<div
    class={` fixed top-16  duration-300 bg-opacity-90 z-[100] border-black border-3 text-black font-bold ${
        clasesByTypeAlert[$alertInfo.type]
    } ${$alertInfo.isOpen ? "opacity-1 px-4 py-2 right-10  " : "opacity-0 -right-20 "}`}
>
    <b>{$alertInfo.message}</b>
</div>
