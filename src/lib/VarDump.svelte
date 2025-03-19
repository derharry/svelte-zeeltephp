<script>

      export let title    = undefined
      export let subtitle = false
      export let vardump  = undefined
      export let debug    = false
      export let dumpJson = false
      export let dumpAll  = false

      // flags :false|any = to know to print title or label
      title    = typeof title    == 'string' ? title    : false;
      subtitle = typeof subtitle == 'string' ? subtitle : false;

      // set fixture when no title and no subtitle is set
      if (!title && !subtitle) {
            //console.log('-- set fixture')
            title = '[VD]title-not-set- example_data';
            vardump = {
                  example1: 'foo',
                  example2: 69,
                  data_object:   { id: 0, name: 'Jingle', 'lastname': 'Bells' },
                  data_array:    [ { id: 1, name: 'Santa', lastname: 'Clause' } ],
                  empty: undefined,
            }
      }

      // add prefix to title when in debug mode
      function get_label(dbgLabl, realLabel) {
            return debug ? `[${debugTitle}]${realLabel}` : realLabel;
      }

      function var_dump() {
            console.log('VD', vardump)
      }
      $: if (title !== false) var_dump(vardump)


</script>



<div   class:borderBox=   {subtitle ? '' : 'borderBox' }>
<table class:tableCompact={subtitle ? '' : 'borderBox' }>

      {#if !subtitle}
            <thead>
                  <tr>
                        <th colspan="3">
                              {title}
                              <small>
                                    :{typeof vardump}
                              </small>

                        </th>
                  </tr>
            </thead>
      {/if}

      <tbody>
      <slot>
      {#if dumpJson} 
            <tr>
                  <td>{JSON.stringify(vardump)}</td>
            </tr>
      {:else if vardump && typeof vardump == 'object' || typeof vardump == 'array'}

            {#each Object.entries(vardump) as [label, value]}
                  {#if (value != null && value != undefined) || dumpAll}
                        <tr>
                        <!-- set label :type value  -->
                              <td width="1" class="nowrap">{get_label('', label)}</td>
                              <td width="1" class="nowrap">
                                    {#if debug}
                                          :{typeof (value)}
                                    {/if}
                              </td>
                              <td class="pad">
                                    {#if !debug && (typeof value == 'object' || typeof value == 'array')}
                                          
                                          {#if Array.isArray(value)}
                                                :array ({value.length})
                                          {:else if typeof value == 'object'}
                                                :object ({Object.entries(value).length})
                                          {/if}       
                                    {:else}
                                          {value}
                                    {/if}
                              </td>
                        </tr>
                        {#if value instanceof Array && !subtitle}
                              <tr>
                                    <td colspan="3" class="pad">
                                          <svelte:self subtitle={label} vardump={value} {debug}/>
                                    </td>
                              </tr>
                        {:else if value instanceof Object}
                              <tr>
                                    <td colspan="3" class="pad">
                                          <svelte:self subtitle={label} vardump={value} {debug}/>
                                    </td>
                              </tr>
                        {:else if false}
                              <tr>
                                    <td>--tbd--empty-block-template for new condition/type</td>
                              </tr>
                        {/if}
                  {/if}
            {/each}

      {:else if vardump && typeof vardump == 'array'}
            <tr>
                  <td>[tbd-array]{JSON.stringify(vardump)}</td>
            </tr>
      {:else}
            <tr>
                  <td>{JSON.stringify(vardump)}</td>
            </tr>
      {/if}
      </slot>
      </tbody>

</table>
</div>