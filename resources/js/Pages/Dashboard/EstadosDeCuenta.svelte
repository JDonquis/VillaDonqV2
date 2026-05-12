<script>
    import BalanceBar from "../../components/BalanceBar.svelte";
    import Table from "../../components/Table.svelte";

    export let data = [];
    console.log(data.students.data);
    console.table(data);
</script>

<svelte:head>
    <title>Estados de Cuenta</title>
</svelte:head>

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
                <td
                    >{student.representative.user.name}
                    {student.representative.user.last_name}</td
                >
                <td> <BalanceBar balances={student.balances} /> </td>
            </tr>
        {/each}
    </tbody></Table
>
