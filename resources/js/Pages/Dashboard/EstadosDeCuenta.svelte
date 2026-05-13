<script>
    import BalanceBar from "../../components/BalanceBar.svelte";
    import Search from "../../components/Search.svelte";
    import Table from "../../components/Table.svelte";

    export let data = [];
    console.log(data.students.data);
    console.table(data);
</script>

<svelte:head>
    <title>Estados de Cuenta</title>
</svelte:head>


<Search placeholder="Buscar estudiante..." class="mb-4" />
<!-- svelte-ignore missing-declaration -->
<Table serverSideData={data?.students.data} pagination={true}>
    <thead slot="thead">
        <tr>
            <th>Estudiante</th>
            <th>Rep Legal</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody slot="tbody">
        {#each data?.students.data as student}
            <tr>
                <td class=" space-y-2">
                        <div class="flex items-center gap-2">
                            <span>
                        
                                {student.name}
                                {student.last_name}
                                <span class="text-gray-500">
                                    | C.I:
                                    {student.ci}
                                    | {student.course.name}-{student.section
                                        .name}
                                </span>
                            </span>
                        </div>
                </td>
                <td class="group"
                    >{student.representative.user.name}
                    {student.representative.user.last_name}
                    <button class="text-green cursor-pointer p-1 hover:bg-gray-100 hidden group-hover:inline-flex">
                        <iconify-icon icon="ic:baseline-whatsapp" width="16" height="16"></iconify-icon>
                    </button>
                    </td
                >
                <td> <BalanceBar balances={student.balances.map((b) => ({ ...b, ...b.months }))} /> </td>
            </tr>
        {/each}
    </tbody></Table
>
