<script>
    import { is_array } from "./zeeltephp/dev.types";

      export let title    = undefined
      export let subtitle = false
      export let vardump  = undefined
      export let debug    = true
      export let debugTitle = ''
      export let dumpJson = false
      export let dumpAll  = false
      export let _depth   = 0
      export let maxDepth = 2

      // flags :false|any = to know to print title or label
      title    = typeof title    == 'string' ? title    : false;
      subtitle = typeof subtitle == 'string' ? subtitle : false;


      // add prefix to title when in debug mode
      function get_label(dbgLabl, realLabel) {
            try {
                  return debug ? `[${debugTitle}#${_depth}]${realLabel}` : realLabel;
            }
            catch (error) {
                  console.log(error);
            }
      }
      function isArray(variable) {
            return Array.isArray(variable) ? true : false;
      }
      function get_typeof(value) {
            try {
                  if (value == undefined) return 'undefined';
                  if (value == null) return 'null';
                  if (isArray(value)) return 'array';
                  if (typeof value == 'function') return 'function';
                  if (typeof value == 'string') return 'string';
                  if (typeof value == 'number') return 'number';
                  if (typeof value == 'boolean') return 'boolean';
                  if (typeof value == 'symbol') return 'symbol';
                  if (typeof value == 'bigint') return 'bigint';
                  if (typeof value == 'object') return 'object';
                  return typeof value;
            }
            catch (error) {
                  console.log(error);
            }
      }

</script>


<div   class:borderBox=   {subtitle ? '' : 'borderBox' }>
<table class:tableCompact={subtitle ? '' : 'borderBox' }>
      
      {#if _depth == 0}
            <thead>
                  <tr>
                        <th colspan="3">
                              {title} 
                              <small> :{get_typeof(vardump)}</small>
                        </th>
                  </tr>
            </thead>
      {/if}

      <tbody>
            {#if dumpJson} 
                  <tr>
                        <td>j{JSON.stringify(vardump)}</td>
                  </tr>
            {:else if vardump && Array.isArray(vardump)}
                  <tr>
                        <td>Array</td>
                  </tr>
            {:else if vardump && get_typeof(vardump) == 'object'}
                  {#each Object.entries(vardump) as [label, value], id1}
                        <tr>
                              <td width="1" class="nowrap">{get_label('', label)}</td>
                              <td width="1" class="nowrap">
                                    {#if debug}
                                          :{get_typeof(value)}
                                    {/if}
                              </td>
                              <td class="pad">
                                    {#if isArray(value)}
                                          :array ({value.length})
                                    {:else if get_typeof(value) == 'object'}
                                          :object ({Object.entries(value).length})
                                    {:else}
                                          {#if get_typeof(value) == 'undefined'}
                                                <span class="error">-undefined-</span>
                                          {:else if get_typeof(value) == 'null'}
                                                <span class="error">-null-</span>
                                          {:else}
                                                {value}
                                          {/if}
                                    {/if}
                              </td>
                        </tr>
                        {#if get_typeof(value) == 'object' && Object.entries(value).length > 0}
                              {#each Object.entries(value) as [_key, _val]}
                                    <tr>
                                          <td class="pad" colspan="3">
                                                {get_typeof(_key)}
                                                {_key} {_val}
                                                <svelte:self vardump={ _val } _depth={_depth +1} {maxDepth} />
                                          </td>
                                    </tr>
                              {/each}

                        {/if}
                  {/each}
                  <!--
                  {#each Object.entries(vardump) as [label, value], id1}
                        <tr>
                              <td width="1" class="nowrap">{get_label('', label)}</td>
                              <td width="1" class="nowrap">
                                    {#if debug}
                                          :{get_typeof(value)}
                                    {/if}
                              </td>
                              <td class="pad">
                                    {#if isArray(value)}
                                          :array ({value.length})
                                    {:else if get_typeof(value) == 'object'}
                                          :object ({Object.entries(value).length})
                                    {:else}
                                          {#if get_typeof(value) == 'undefined'}
                                                <span class="error">-undefined-</span>
                                          {:else if get_typeof(value) == 'null'}
                                                <span class="error">-null-</span>
                                          {:else}
                                                {value}
                                          {/if}
                                    {/if}
                              </td>
                        </tr>
                        {#if _depth < maxDepth && value != null || value != undefined}
                              {#if isArray(value)}
                                    <tr>
                                          <td class="pad">
                                                ARRAY
                                          </td>
                                    </tr>
                              {:else if get_typeof(value) == 'object' && Object.entries(value).length > 1}
                                    <tr>
                                          <td class="pad">
                                                OBJECT
                                                {#each Object.entries(value) as entry}
                                                      <svelte:self subtitle="x" vardump={entry} _depth={_depth+1} {maxDepth} />
                                                {/each} 
                                          </td>
                                    </tr>
                              {:else}
                                    <!--
                                    <tr>
                                          <td>-> {get_typeof(value)} {value}</td>
                                    </tr>
                                    -- >
                              {/if}
                              <!--
                              {#if isArray(value) && value.length > 1} 
                                    <tr>
                                          <td>Array</td>
                                    </tr>
                              {:else if typeof value == 'object' && Object.entries(value).length > 1}
                                    <tr>
                                          <td class="pad">
                                                {#each Object.entries(value) as [_label, _value], id2}
                                                      <svelte:self subtitle={_label} vardump={_value} {debug} _depth={++_depth} {maxDepth} id={id2}/>
                                                {/each}
                                          </td>
                                    </tr>
                              {/if}
                              - ->
                        {/if}
                  {/each}
                  -->
            {:else}
                        <tr>
                              <td width="1" class="nowrap">{get_label('', vardump)}</td>
                              <td width="1" class="nowrap">
                                    {#if debug}
                                          :{get_typeof(vardump)}
                                    {/if}
                              </td>
                              <td>
                                    {vardump}
                              </td>
                              <!--
                              <td width="1" class="nowrap">
                                    {#if debug}
                                          :{get_typeof(vardump)}
                                    {/if}
                              </td>
                              <td class="pad">
                                    {#if isArray(vardump)}
                                          :array ({vardump.length})
                                    {:else if get_typeof(vardump) == 'object'}
                                          :object ({Object.entries(vardump).length})
                                    {:else}
                                          {#if get_typeof(vardump) == 'undefined'}
                                                <span class="error">-undefined-</span>
                                          {:else if get_typeof(vardump) == 'null'}
                                                <span class="error">-null-</span>
                                          {:else}
                                                {vardump}
                                          {/if}
                                    {/if}
                              </td>
                              -->
                        </tr>
            {/if}
      </tbody>

</table>
</div>



<!--
<script>

      export let title    = undefined
      export let subtitle = false
      export let vardump  = undefined
      export let debug    = true
      export let debugTitle = ''
      export let dumpJson = false
      export let dumpAll  = false
      export let _depth   = 0
      export let maxDepth = 5

      // flags :false|any = to know to print title or label
      title    = typeof title    == 'string' ? title    : false;
      subtitle = typeof subtitle == 'string' ? subtitle : false;

      // set fixture when no title and no subtitle is set
      if (!title && !subtitle) {
            try {
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
            catch (error) {
                  console.log(error);
            }
      }

      function is_nullish(variable) {
            return variable === null || variable === undefined ? true : false
      }

      function var_dump() {
            if (title !== false)
            console.log('VD', title, vardump)
      }
      //$: if (title !== false) var_dump(vardump)

      console.log('VD', title, subtitle, _depth, maxDepth, subtitle, vardump);

</script>


<div   class:borderBox=   {subtitle ? '' : 'borderBox' }>
<table class:tableCompact={subtitle ? '' : 'borderBox' }>

      {#if !subtitle}
            <thead>
                  <tr>
                        <th colspan="3">
                              {title} 
                              <small> :{typeof vardump}</small>
                        </th>
                  </tr>
            </thead>
      {/if}

      <tbody>
      <slot>
      {#if dumpJson} 
            <tr>
                  <td>j{JSON.stringify(vardump)}</td>
            </tr>
      {:else if Array.isArray(vardump)}
            <tr>
                  <!-- set label :type value  -- >
                  <td width="1" class="nowrap">array</td>

            </tr>
      {:else if vardump && typeof vardump == 'object'}
            {#each Object.entries(vardump) as [label, value], id1}
                        <tr>
                              <!-- set label :type value  -- >
                              <td width="1" class="nowrap">{get_label('', label)}</td>
                              <td width="1" class="nowrap">
                                    {#if debug}
                                          :{get_typeof(value)}
                                    {/if}
                              </td>
                        </tr>
                  
                  {#if _depth <= maxDepth}
                  {#if isArray(value)}
                        <tr>
                              <td>Array</td>
                        </tr>
                  {:else if get_typeof(value) == 'object'}
                        <tr>
                              <td colspan="3" class="pad">
                                    {#each Object.entries(value) as [_label, _value],id2}
                                          xxx<svelte:self title={_label} vardump={_value} {debug} _depth={++_depth} {maxDepth} id={id2}/>
                                    {/each}
                              </td>
                        </tr>
                  {:else if true}
                        <tr>
                              <td>--tbd--empty-block-template for new condition/type</td>
                        </tr>
                  {/if}
                  {/if}
            {/each}

      <!--
            {#each Object.entries(vardump) as [label, value]}
                  {#if (!is_nullish(value) || dumpAll)}
                        <tr>
                        <!-- set label :type value  -- >
                              <td width="1" class="nowrap">{get_label('', label)}</td>
                              <td width="1" class="nowrap">
                                    {#if debug}
                                          :{typeof (value)}
                                    {/if}
                              </td>
                              <td class="pad">
                                    {#if !debug && (typeof value == 'object' || typeof value == 'array')}
                                          {#if !is_nullish(value)}
                                                {#if Array.isArray(value)}
                                                      :array ({value.length})
                                                {:else if typeof value == 'object'}
                                                      :object ({Object.entries(value).length})
                                                {/if}
                                          {/if}
                                    {:else}
                                          {value}
                                    {/if}
                              </td>
                        </tr>
                        {#if Array.isArray(value) }
                              <tr>
                                    <td colspan="3" class="pad">
                                         Array <!-- <svelte:self subtitle={label} vardump={value} {debug}/> -- >
                                    </td>
                              </tr>
                        {:else if typeof value == 'object'}
                              <tr>
                                    <td colspan="3" class="pad">
                                          {#if _depth < maxDepth}
                                               ({_depth}) <svelte:self subtitle={label} vardump={value} {debug} _depth={++_depth} {maxDepth}/>
                                          {:else}
                                                <span class="error">[maxDepth reached]</span>
                                                <svelte:self subtitle={label} {debug} _depth={maxDepth+1} {maxDepth}/>
                                          {/if}

                                    </td>
                              </tr>
                        {:else if false}
                              <tr>
                                    <td>--tbd--empty-block-template for new condition/type</td>
                              </tr>
                        {/if}
                  {/if}
            {/each}
      -- >
      {:else if vardump && typeof vardump == 'array'}
            <tr>
                  <td>[tbd-array]{JSON.stringify(vardump)}</td>
            </tr>
      {:else}
            <tr>
                  <td>-{JSON.stringify(vardump)}</td>
            </tr>
      {/if}
      </slot>
      </tbody>

</table>
</div>

-->