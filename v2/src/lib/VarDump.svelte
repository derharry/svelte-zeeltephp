<script>

      import { is_typeof, get_typeof, var_dump } from "./zeeltephp/dev.types";

      // props
      export let title    = undefined
      export let subtitle = false
      export let vardump  = undefined 
      export let debug    = false
      export let dumpJson = false
      export let dumpAll  = false

      // flags :false|any = to know to print title or label
      title    = is_typeof('string', title)    ? title    : false;
      subtitle = is_typeof('string', subtitle) ? subtitle : false;

      // add prefix to title when in debug mode
      function get_label(dbgLabl, realLabel) {
            return debug ? `[${debugTitle}]${realLabel}` : realLabel;
      }

      // set fixture when
      if (!title && !subtitle) {
            title = '[VD]title-not-set- example_data';
            vardump = {
                  example1: 'foo',
                  example2: 69,
                  data_object:   { id: 0, name: 'Jingle' },
                  data_array:    [ 0, 'Santa', 'Clause' ],
            }
      }

      //$: if (!subtitle) var_dump(vardump, title)
      //$: if (!subtitle) console.log('VD ', title, {vardump})
      //console.log('VD  ', subtitle ? ' -- '+subtitle : title, {vardump})


</script>

<div   class:borderBox=   {subtitle ? '' : 'borderBox' }>
<table class:tableCompact={subtitle ? '' : 'borderBox' }>
      <thead>
            {#if !subtitle}
                  <tr>
                        <th colspan="3" class="nowrap">
                              {title}
                              <small> 
                                    :{get_typeof(vardump)}/{typeof(vardump)}
                  
                                    <!--
                                    ({Object.entries(vardump)?.length})
                                    <br>
                                    ={is_typeof('PointerEvent', vardump) ? 'true':'false'}
                                    -->
                              </small>
                        </th>
                  </tr>
            {/if}
      </thead>

      <tbody>
            <slot>
      
                  <!--
                  -->
                  
                  {#if dumpJson} 
                        <tr>
                              <td>{JSON.stringify(vardump)}</td>
                        </tr>
                  {:else if vardump && typeof vardump == 'object'}
                        {#each Object.entries(vardump) as [label, value]}

                              {#if value || dumpAll}
                                    <tr>
                                          <!--  set label :type value  -->
                                          <td width="1" class="nowrap">{get_label('', label)}</td>
                                          <td width="1" class="nowrap">
                                                {#if debug}
                                                      :{get_typeof(value)}
                                                {/if}
                                          </td>
                                          <td class="pad">
                                                {#if !debug && (is_typeof('object', value) || is_typeof('array', value))}
                                                      
                                                      {#if Array.isArray(value)}
                                                            :array ({value.length})
                                                      {:else if is_typeof('object', value)}
                                                            :object ({Object.entries(value).length})
                                                      {/if}       
                                                {:else}
                                                      {value}
                                                {/if}
                                          </td>
                                    </tr>
                                    {#if vardump instanceof Array && !subtitle}
                                          <tr>
                                                <td colspan="3" class="pad">
                                                      <svelte:self subtitle={label} {vardump} {debug}/>
                                                </td>
                                          </tr>
                                    {:else if value instanceof Array || value instanceof Object}
                                          <tr>
                                                <td colspan="3" class="pad">
                                                      <svelte:self subtitle={label} vardump={value} {debug}/>
                                                </td>
                                          </tr>
                                    {:else}
                                          <!-- not any!
                                          <tr>
                                                <td colspan="3">
                                                      Fallback:<br>
                                                      {JSON.stringify(vardump)}
                                                </td>
                                          </tr>
                                          -->
                                    {/if}

                              {/if}
                        {/each}
                  {:else if vardump && typeof vardump == 'array'}
                        <tr>
                              <td>[tbd]{JSON.stringify(vardump)}</td>
                        </tr>
                  {:else}
                        <tr>
                              <td>{JSON.stringify(vardump)}</td>
                        </tr>
                  {/if}
                  <!--
                  {#if dumpJson} 
                        //nothing todo
                  {:else if false}
                        {#if is_typeof('object', vardump) || is_typeof('array', vardump)}


                              <!--
                              {#each Object.entries(vardump) as [label, value]}
                                    <tr>
                                          <td width="1" class="nowrap">{get_label('', label)}</td>
                                          <td width="1" class="nowrap">
                                                {#if debug}
                                                      :{get_typeof(value)}
                                                {/if}
                                          </td>
                                          <td class="pad">
                                                {#if !debug && (is_typeof('object', value) || is_typeof('array', value))}
                                                      :{get_typeof(value)}
                                                      {#if is_typeof('array', value)}
                                                            ({value.length})
                                                      {:else if is_typeof('object', value)}
                                                            ({Object.entries(value).length})
                                                      {/if}       
                                                {:else}
                                                      {value}
                                                {/if}
                                          </td>
                                    </tr>
                                    {#if vardump instanceof Array && !subtitle}
                                          <tr>
                                                <td colspan="3" class="pad">
                                                      <svelte:self subtitle={label} {vardump} {debug}/>
                                                </td>
                                          </tr>
                                    {:else if value instanceof Array || value instanceof Object}
                                          <tr>
                                                <td colspan="3" class="pad">
                                                      <svelte:self subtitle={label} vardump={value} {debug}/>
                                                </td>
                                          </tr>
                                    {:else}
                                          <!-- not any!
                                          <tr>
                                                <td colspan="3">
                                                      Fallback:<br>
                                                      {JSON.stringify(vardump)}
                                                </td>
                                          </tr>
                                          -- >
                                    {/if}
                              {/each}
                              -- >
                        {:else if false}
                              <tr>
                                    <td>--tbd--empty-block-template for new condition/type</td>
                              </tr>
                        {:else}
                              <tr>
                                    <td>{JSON.stringify(vardump)}</td>
                              </tr>
                        {/if}
                  {/if}

      <!--

                  {#if is_typeof('object', vardump) || is_typeof('array', vardump)}                   

                        {#each Object.entries(vardump) as [label, value]}
                              
                              <tr>
                                    <td width="1" class="nowrap">{get_label('', label)}</td>
                                    <td width="1" class="nowrap">
                                          {#if debug}
                                                :{get_typeof(value)}
                                          {/if}
                                    </td>
                                    <td class="pad">
                                          {#if !debug && is_typeof('array|object', value)}
                                                :{get_typeof(value)}
                                                {#if is_typeof('array', value)}
                                                      ({value.length})
                                                {:else if is_typeof('object', value)}
                                                      ({Object.entries(value).length})
                                                {/if}       
                                          {:else}
                                                {value}
                                          {/if}
                                    </td>
                              </tr>

                              {#if vardump instanceof Array && !subtitle}
                                    <tr>
                                          <td colspan="3" class="pad">
                                                <svelte:self subtitle={label} {vardump} {debug}/>
                                          </td>
                                    </tr>
                              {:else if value instanceof Array || value instanceof Object}
                                    <tr>
                                          <td colspan="3" class="pad">
                                                <svelte:self subtitle={label} vardump={value} {debug}/>
                                          </td>
                                    </tr>
                              {:else}
                                    <!-- not any!
                                    <tr>
                                          <td colspan="3">
                                                Fallback:<br>
                                                {JSON.stringify(vardump)}
                                          </td>
                                    </tr>
                                    -- >
                              {/if}
                        {/each}

                  {:else}

                        <tr>
                              <td>{JSON.stringify(vardump)}</td>
                        </tr>

                  {/if}
      -->
            </slot>
      </tbody>
</table>
</div>