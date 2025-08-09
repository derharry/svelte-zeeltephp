<script>
// ZPDevNav.svelte
     import './zpdev.css';
     import { get } from 'svelte/store';
     import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
     import HTML_Marquee              from "$lib/zeelte/HTML_Marquee.svelte";

     import { data, form, error }     from "$lib/zeeltephp/zp.fetch.js"
          import { 
          showApp, showDumpPanel,
          appTabs, dumpTabs,
          dumpTabsHasData
     } from "./zpdev.stores.js";

     let {
          promise_fetch = $bindable()
     } = $props();

     const debug = true

     const clickSet = (e, store, value) => {
          e.preventDefault();
          store.set(value);
     };

</script>

<div class="frameHeader svcolor-header">

     <span class="zp-title">
          ZP Dev
          {#await promise_fetch}
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
                    class:activebg={$showApp === t.key}
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
                    class:active={$showDumpPanel === t.key} 
                    class:activebg={$dumpTabsHasData.find(v => v.key === t.key)?.hasData === true}
                    onclick={(e) => clickSet(e, showDumpPanel, t.key)}
               >{t.label} </button>
          {/each}
     </div>

</div>