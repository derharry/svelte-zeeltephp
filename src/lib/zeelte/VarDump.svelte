<script>
/**
 * VarDump.svelte
 * 
 * A recursive Svelte component for visualizing and debugging any JavaScript value or object,
 * similar to PHP's var_dump. Supports depth control, custom styles, and JSON output.
 * 
 * Props:
 * - title:      (string)   Optional title for the dump
 * - vardump:    (any)      The value to dump (object, array, primitive, etc.)
 * - dumpAll:    (boolean)  Show all properties, even undefined/null (default: false)
 * - dumpJson:   (boolean)  Show JSON representation instead of table (default: false)
 * - debug:      (boolean)  Enable debug mode (adds type info and debug labels)
 * - debugTitle: (string)   Prefix for debug labels
 * - _depth:     (number)   Current recursion depth (internal)
 * - maxDepth:   (number)   Maximum recursion depth (default: 5)
 * - cssStyle:   (string)   Custom CSS for the border box
 * - dumpConsole:(boolean)  If true, log vardump to console
 * - noBorder:   (boolean)  If true, disables border styling
 */


      export let title = undefined;
      export let vardump = undefined;   
      export let dumpAll = false;
      export let dumpJson = false;
      export let debug = false;
      export let debugTitle = "";
      export let _depth = 0;   // Current recursion depth
      export let maxDepth = 5; // Maximum recursion depth
      export let cssStyle = "";
      export let dumpConsole = false;
      export let noBorder = false;

      /** Helper: Determine the type of a value */
      function get_typeof(value) {
            if (value === undefined) return "undefined";
            if (value === null) return "null";
            if (isArray(value)) return "array";
            if (typeof value === "function") return "function";
            if (typeof value === "string") return "string";
            if (typeof value === "number") return "number";
            if (typeof value === "boolean") return "boolean";
            if (typeof value === "symbol") return "symbol";
            if (typeof value === "bigint") return "bigint";
            //if (typeof value === 'object' && value.constructor && value.constructor.name !== 'Object') return value.constructor.name;
            if (typeof value === "object") return "object";
            return typeof value;
      }

      /** Helper: Is value an array? */
      function isArray(variable) {
            return Array.isArray(variable);
      }

      /** Helper: Is value a plain object? */
      function isObject(value) {
            return (
                  value !== null &&
                  typeof value === "object" &&
                  !Array.isArray(value)
            );
      }

      /** add prefix to title when in debug mode */
      function get_label(dbgLabl, realLabel) {
            return realLabel;
            return debug
                  ? `[${dbgLabl}#${_depth}] ${realLabel}`
                  : realLabel;
      }

      /** Optionally log to console */
      $: if (dumpConsole) console.log(`VD ${title}`, {vardump});
</script>



<div class:borderBox={_depth == 0 && !noBorder} style="{cssStyle}">
<table>

{#if _depth === 0}
      <thead>
      <tr>
            <th>{title || "[VD] DEBUG"} &nbsp; <small>: {get_typeof(vardump)}</small></th>
            <th style="text-align:right">
                  <button>dump/json</button>
                  <button>show-full</button>
            </th>
      </tr>
      </thead>
{/if}

<tbody>
<tr><td colspan="2">
<table><tbody>

{#if dumpJson}

      <tr><td colspan="3"><pre style="overflow:auto">{JSON.stringify(vardump, null, 2)}</pre></td></tr>

{:else if vardump == undefined}

      <tr><td colspan="3"></td></tr>

{:else if vardump == null}
      <tr>
            <td width="1" class="nowrap">e{get_label("", vardump)}</td>
            <td width="1" class="nowrap">{#if debug}:{get_typeof(vardump)}{/if}</td>
            <td>null</td>
      </tr>
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
                  <svelte:self vardump={item} _depth={_depth + 1} {maxDepth} {debug} />
            </td>
            </tr>
      {/each}

{:else if vardump && get_typeof(vardump) == "object"}

      {#each Object.entries(vardump) as [label, value], id1}
            {#if (get_typeof(value) != "undefined" && get_typeof(value) != "null") || dumpAll}
                  <tr>
                        <td width="1" class="nowrap">{get_label("", label)}</td>
                        <td width="1" class="nowrap">{#if debug}:{get_typeof(value)}{/if}</td>
                        <td class="pad">
                              {#if isArray(value)}
                                  :array ({value.length})
                              {:else if get_typeof(value) === "object"}
                                :object ({Object.entries(value).length})
                              {:else if get_typeof(value) === "undefined"}
                                  <span class="error">:undefined</span>
                              {:else if get_typeof(value) === "null"}
                                  <span class="error">:null</span>
                              {:else}
                                {value}
                              {/if}
                        </td>
                  </tr>
                  {#if get_typeof(value) === "object" && Object.entries(value).length > 0}
                        <tr>
                              <td class="pad" colspan="3" style="padding-left:1em;">
                                    <svelte:self vardump={value} _depth={_depth + 1} {maxDepth} {debug} />
                              </td>
                        </tr>
                  {/if}
            {/if}
      {/each}

{:else}
      <tr>
            <td width="1" class="nowrap">{get_label("", vardump)}</td>
            <td width="1" class="nowrap">{#if debug}:{get_typeof(vardump)}{/if}</td>
            <td>{vardump}</td>
      </tr>
{/if}

</tbody></table>
</td></tr>
</tbody>
</table>
</div>


<style>   
table {
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}
table td {
    padding: 0 !important;
    margin: 0 !important;
    vertical-align: top;
}
.pad {
      padding-left:1em !important;
}
</style>