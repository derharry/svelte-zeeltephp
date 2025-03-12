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
                              <td width="1" class="nowrap">{label}</td>
                              <td>{value}</td>
                        </tr>
                  {/each}
            </slot>
      </tbody>
</table>