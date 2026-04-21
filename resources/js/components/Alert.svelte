<script>
    import { alertInfo } from "../stores/alertStore";
    const clasesByTypeAlert = {
        success: "bg-green text-black",
        info: "bg-white text-black",
        error: "bg-red text-black",
    };
</script>

<div
    class={`fixed top-4 duration-150 border-3 border-black right-10 z-[9999] font-bold ${
        clasesByTypeAlert[$alertInfo.type]
    } ${$alertInfo.isOpen ? "opacity-100 px-4 py-2 right-96" : "opacity-0 pointer-events-none"}`}
    style="z-index: 99999;"
>
    <b>{$alertInfo.message}</b>
</div>
