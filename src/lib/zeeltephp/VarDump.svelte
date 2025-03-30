<script>

      export let title   = undefined;
      export let vardump = undefined;
      export let dumpAll = false;
      export let dumpJson = false;
      export let debug   = false;
      export let debugTitle = ''
      export let _depth  = 0; // Current recursion depth
      export let maxDepth = 2; // Maximum recursion depth

      // Helper functions
      function get_typeof(value) {
            if (value === undefined) return 'undefined';
            if (value === null) return 'null';
            if (isArray(value)) return 'array';
            if (typeof value === 'function') return 'function';
            if (typeof value === 'string') return 'string';
            if (typeof value === 'number') return 'number';
            if (typeof value === 'boolean') return 'boolean';
            if (typeof value === 'symbol') return 'symbol';
            if (typeof value === 'bigint') return 'bigint';
            //if (typeof value === 'object' && value.constructor && value.constructor.name !== 'Object') return value.constructor.name;
            if (typeof value === 'object') return 'object';
            return typeof value;
      }


      function isArray(variable) {
            return Array.isArray(variable);
      }
      function isObject(value) {
            return value !== null && typeof value === 'object' && !Array.isArray(value);
      }

      // add prefix to title when in debug mode
      function get_label(dbgLabl, realLabel) {
            try {
                  return debug ? `[${debugTitle}#${_depth}] ${realLabel}` : realLabel;
            }
            catch (error) {
                  console.log(error);
            }
      }
</script>


<div   class:borderBox={_depth == 0}>
<table class:tableCompact={_depth == 0}>
      {#if _depth === 0}
            <thead>
                  <tr>
                  <th colspan="3">
                  {title || '[VD] DEBUG'}
                  <small>: {get_typeof(vardump)}</small>
                  </th>
                  </tr>
            </thead>
      {/if}

      <tbody>
            {#if dumpJson}
                  <tr>
                        <td><pre style="overflow:auto">{JSON.stringify(vardump, null, 2)}</pre></td>
                  </tr>
            <!--
                  {:else if vardump instanceof URL}
                  <tr>
                    <td colspan="3">
                      <strong>URL:</strong> {vardump.href}
                      <div class="pad">
                        Protocol: {vardump.protocol}<br>
                        Host: {vardump.host}<br>
                        Path: {vardump.pathname}<br>
                        Search: {vardump.search}
                      </div>
                    </td>
                  </tr>
                  {:else if vardump instanceof URLSearchParams}
                  <tr>
                    <td colspan="3">
                      <strong>URLSearchParams:</strong>
                      <svelte:self vardump={Object.fromEntries(vardump.entries())} 
                               _depth={_depth + 1} {maxDepth} {debug} />
                    </td>
                  </tr>
            -->                
                {:else if vardump instanceof Date}
                  <tr>
                    <td colspan="3">
                      <strong>Date:</strong> {vardump.toISOString()}
                    </td>
                  </tr>                                    
            {:else if vardump && Array.isArray(vardump)}
                  <tr>
                        <td>Array</td>
                  </tr>
                  {#each vardump as item}
                        <tr>
                              <td class="pad" colspan="3">
                                    <svelte:self vardump={item} _depth={_depth + 1} {maxDepth} />
                              </td>
                        </tr>
                  {/each}
            {:else if vardump && get_typeof(vardump) === 'object'}
                  {#each Object.entries(vardump) as [label, value], id1}
                        {#if (get_typeof(value) != 'undefined' && get_typeof(value) != 'null') || dumpAll}
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
                                          {:else if get_typeof(value) === 'object'}
                                                :object ({Object.entries(value).length})
                                          {:else}
                                                {#if get_typeof(value) === 'undefined'}
                                                      <span class="error">:undefined</span>
                                                {:else if get_typeof(value) === 'null'}
                                                      <span class="error">:null</span>
                                                {:else}
                                                      {value}
                                                {/if}
                                          {/if}
                                    </td>
                              </tr>
                              {#if get_typeof(value) === 'object'}
                                    <tr>
                                          <td class="pad" colspan="3" style="padding-left:1em;">
                                                <svelte:self vardump={value} _depth={_depth + 1} {maxDepth} {debug} />
                                          </td>
                                    </tr>
                              {/if}
                        {:else}
                              <!--
                              <tr>
                                    <td>{label} = {value}</td>
                              </tr>
                              -->
                        {/if}
                  {:else}
                              <tr>
                                    <td>{vardump}</td>
                              </tr>
                  {/each}
            {:else}
                  <tr>
                        <td width="1" class="nowrap">{get_label('', vardump)}</td>
                        <td width="1" class="nowrap">??
                              {#if debug}
                                    :{get_typeof(vardump)}
                              {/if}
                        </td>
                        <td>{vardump}</td>
                  </tr>
            {/if}
      </tbody>
</table>
</div>



<!--
<style>
/*
.center {
      text-align: center;
      justify-content: center;
}
.right {
      text-align: right;
      justify-content: right;
}

.top {
      vertical-align: top;
}

.nowrap {
      white-space: nowrap;
}
.wrap {
      white-space: normal;
}

.pad {
      padding-left: 1.5em !important;
}

.defaultPadding {
      padding: 0em 1em !important;
}
.borderBox {
      border: 1px blue solid !important;
      overflow: hidden;
      border-radius: 1em !important;
      padding: 0em 1em;
}
.tableCompact {
      font-size: 1em;
}

.tableCompact THEAD TH, .tableTHEAD {
      padding-left: 0;
      font-style:   italic;
      font-weight:  900;
}
.tableCompact TBODY {
      font-size: 0.9em;
}
.tableCompact TBODY TD {
      padding: 0;
      margin:  0;
      vertical-align: top;
}
.tableCompact TBODY TD:first-child {
      text-wrap: no-wrap;
      padding-right: 1em;
}
.tableCompact INPUT, 
.tableCompact BUTTON {
      padding: 0;
      margin: 0;
      height: 1em;
      font-size: 1em;
      height: 1.75em;
}


.td33 {
      width: 33%;
      max-width: 33%;
}
.td50 {
      width: 50%;
      max-width: 50%;
}

.flexRow,
.zpFlexRow {
      display: flex;
      flex-direction: row;
}

.routeBar {
      background-color: navy;
      color: grey;
      
      padding: 0.25em 1em;
      margin:  0;
      margin-bottom:  1.25em;
}

.positioningParent {
      position: relative;
}
.positioningChild {
      position: absolute;
}

h1 {
      font-size: 1.25em;
}


.layoutApp_off {
      max-width:850px; 
      margin:auto;
}

BUTTON {
      padding-left:  0.5em;
      padding-right: 0.5em;
}
*/
</style>
-->