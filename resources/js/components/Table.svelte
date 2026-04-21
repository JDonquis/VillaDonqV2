<script>
    import { inertia } from "@inertiajs/svelte";
    import { router } from "@inertiajs/svelte";
    import { page } from "@inertiajs/svelte";
    import debounce from "lodash/debounce";
    import { createEventDispatcher } from "svelte";

    const dispatch = createEventDispatcher();

    export let filtersOptions = [];
    export let selectedRow;
    export let serverSideData = {};
    export let pagination = true;
    export let allowFilters = true;

    let filterClientData = {
        search: new URLSearchParams($page.url.split('?')[1] || '').get('search') || '',
        ...serverSideData.filters,
    };
    // $: $form, handleFilters()

    const handleFilters = () => {
        router.get(`${$page.url}`, filterClientData, { preserveState: true, replace: true });
    };

    const handleSearch = debounce(() => {
        router.get(`${$page.url}`, filterClientData, { preserveState: true, replace: true });
    }, 300);
</script>

<section class="w-full">
    <div class="mt-6 md:flex md:items-center md:justify-between">
        <div class="flex gap-2 md:gap-7">
            <div
                class={`inline-flex overflow-hidden  ${allowFilters ? "border border-black  divide-x divide-black" : ""}  rtl:flex-row-reverse" : ""}`}
            >
                <!-- <button
                    on:click={(e) => {
                        filterClientData["status"] = "";
                        handleFilters();
                    }}
                    class="px-5 py-2 text-xs font-medium text-gray-600 transition-colors duration-200 sm:text-sm bg-gray-200 hover:bg-gray-100"
                    class:bg-gray-200={filterClientData["status"] == "" ||
                        !filterClientData["status"]}
                >
                    Todos
                </button> -->
                {#each Object.entries(filtersOptions) as [filterKey, filterOption]}
                    {#each filterOption as filter, i}
                        <button
                            on:click={(e) => {
                                filterClientData[filterKey] = filter.id;
                                handleFilters();
                            }}
                            class="px-5 font-semibold py-2 text-xs bg-background text-gray-600 transition-colors duration-200 sm:text-sm hover:bg-gray-100"
                            class:bg-green={serverSideData.filters[filterKey] ==
                                filter.id ||
                                (i == 0 && !filterClientData[filterKey])}
                        >
                            {filter.name}
                        </button>
                    {/each}
                {/each}
            </div>
            <slot name="filterBox"></slot>
        </div>

        <div class="flex  gap-10">
        <div class="relative flex items-center mt-4 md:mt-0 duration-100">
            <span class="absolute">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5 mx-3 text-gray-400"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                    />
                </svg>
            </span>

            <input
                type="search"
                placeholder="Buscar"
                bind:value={filterClientData.search}
                on:input={() => {
                    handleSearch();
                }}
                style="padding-left: 2.5em"
                class="block nb-input md:w-80 placeholder-gray-400/70 pl-11 rtl:pr-11 rtl:pl-5 focus:border-blue-400 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
            />
        </div>
        {#if selectedRow.status}
            <div class="flex fadeIn gap-5 relative items-end">
                <button
                    on:click={() => dispatch("fillFormToEdit")}
                    class="bg-yellow hover:bg-opacity-90 cursor-pointer text-2xl hover:-translate-x-0.5 hover:-translate-y-0.5 hover:medium-shadow border-3 border-black small-shadow px-4 py-1"
                    title="Editar"
                >
                   <iconify-icon icon="ic:baseline-edit" class="relative top-1" width="24" height="24"></iconify-icon>
                </button>

                <button
                    on:click={() => dispatch("clickDeleteIcon")}
                    class="small-shadow border-3 hover:bg-opacity-90 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:medium-shadow border-black bg-red font-bold px-4 py-1 text-2xl"
                    title="Eliminar"
                >
                    <iconify-icon
                        class="relative -bottom-1"
                        icon="material-symbols:delete-outline"
                    ></iconify-icon>
                </button>
            </div>
        {/if}
        </div>
    </div>

    <div class="flex flex-col mt-4">
        <div
            class="-mx-4 -my-2 overflow-x-auto overflow-y-visible sm:-mx-6 lg:-mx-8"
        >
            <div class="inline-block w-full py-2 align-middle md:px-6 lg:px-8">
                <div
                    class="overflow-x-auto  overflow-y-auto scroll-table border bg-white border-gray-200"
                >
                    <table
                        class="table overflow-scroll overflow-y-auto w-full divide-y divide-gray-200"
                    >
                        <slot name="thead"></slot>

                        <slot
                            name="tbody"
                            class="bg-white divide-y divide-gray-200  "
                        ></slot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {#if pagination}
        <!-- Pagination ---------------------------------------------------------------------------------------------- -->
        <div class="mt-2 sm:flex sm:items-center sm:justify-between">
            <div class="text-sm text-gray-500">
                Page <span class="font-medium text-gray-700"
                    >{serverSideData.current_page} of {serverSideData.last_page}</span
                >
            </div>

            <!-- pagination buttons -->
            <div class="flex items-center mt-4 gap-x-4 sm:mt-0">
                <a
                    use:inertia
                    href={serverSideData.prev_page_url}
                    class="flex items-center justify-center w-1/2 px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 disabled:cursor-not-allowed
                    white border sm:w-auto gap-x-2 hover:bg-gray-100"
                    disabled={serverSideData.prev_page_url == null}
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5 rtl:-scale-x-100"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18"
                        />
                    </svg>

                    <span> previous </span>
                </a>

                <a
                    use:inertia
                    href={serverSideData.next_page_url}
                    class="flex items-center justify-center w-1/2 px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border sm:w-auto gap-x-2 hover:bg-gray-100"
                    disabled={serverSideData.next_page_url == null}
                >
                    <span> Next </span>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5 rtl:-scale-x-100"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"
                        />
                    </svg>
                </a>
            </div>
        </div>
    {/if}
</section>

<style>
    /* normal css */
    .scroll-table::-webkit-scrollbar {
        /* width: 17px;   */
        height: 13px;
    }

    .scroll-table::-webkit-scrollbar-track {
        background: rgb(14, 14, 14); /* color of the tracking area */
        /* padding: 2px; */
    }

    .scroll-table::-webkit-scrollbar-thumb {
        background-color: #35475c; /* color of the scroll thumb */
        border-radius: 4px; /* roundness of the scroll thumb */
        border: 3px solid rgb(14, 14, 14);
        border-bottom: 0.2px solid rgb(14, 14, 14);
    }
    .scroll-table::-webkit-scrollbar-thumb:hover {
        background-color: rgb(184, 206, 231);
    }
    .scroll-table::-webkit-scrollbar-corner {
        background: rgba(0, 0, 0, 0.5);
    }
</style>
