<script>

      export let title = 'Example data';
      export let table_data = {
            'example1': 'foo',
            'example2': 'zoo'
      };

</script>

<table class="tableCompact">
      <thead>
            <tr>
                  <th colspan="2">{title}</th>
            </tr>
      </thead>
      <tbody>
            <slot>
                  {#each Object.entries(table_data) as [label, value]} 
                        
                        <tr>
                        {#if value instanceof Array || value instanceof Object }
                              <td colspan="2"><svelte:self title={label} table_data={value} /></td>
                        {:else}
                              <td width="1" class="nowrap">{label}</td>
                              <td>{value}</td>
                        {/if}
                        </tr>

                  {/each}
            </slot>
      </tbody>
</table>