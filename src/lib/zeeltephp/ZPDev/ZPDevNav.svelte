<script>
// ZPDevNav.svelte
     import HTML_Marquee              from "$lib/zeelte/HTML_Marquee.svelte";
     import { 
          promise_fetch,
          data, form, error,
          appTabs, dumpTabs,
          showApp, showDumpPanel,
          dumpTabsHasData
     } from "./zpdev.stores.js";

     const clickSet = (e, store, value) => {
          e.preventDefault();
          store.set(value);
     };
</script>



<div class="frameHeader svcolor-header">

     <span class="zp-title">
          ZP Dev
          {#await $promise_fetch}
               <HTML_Marquee value="🐘" />
          {:then _}
               {#if $error}
                    ⚠️
               {:else if $data || $form}
                    🐘                    
               {:else}
                    ⚠️
               {/if}
          {/await}
     </span>

     <!-- navButtons to show current App -->
     <!-- App nav buttons -->
     <div>
          {#each appTabs as t}
               <button
                    class="tab"
                    class:active={$showApp === t.key}
                    onclick={(e) => clickSet(e, showApp, t.key)}
               >{t.label}</button>
          {/each}
     </div>

     <!-- DumpPanel nav buttons 
          class:activebg={$t.store ?? false} 
          class:activebg={localDumpTabStates.find(v => v.key === t.key)?.hasData}
      -->
     <div>
          {#each dumpTabs as t}
               <button
                    class="tab"
                    class:activebg={$dumpTabsHasData.find(v => v.key === t.key)?.hasData}
                    class:active={$showDumpPanel === t.key} 
                    disabled={!$dumpTabsHasData.find(v => v.key === t.key)?.hasData}
                    onclick={(e) => clickSet(e, showDumpPanel, t.key)}
               >{t.label} </button>
          {/each}
     </div>
</div>