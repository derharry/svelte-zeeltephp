<script>
// ZPDevNav.svelte
     import { HTML_Marquee  } from "zeelte";
     import { page } from '$app/state';
     import { 
          promise_fetch,
          appTabs, dumpTabs,
          showApp, showDumpPanel,
          dumpTabsHasData
     } from "./zpdev.stores.js";
     import { data, form, error } from 'zeeltephp';

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
          {:catch}
                    ⚠️
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

     <div>
          <a href="http://localhost:5173/api?{page.url.pathname || page.route.id}" target="_blank">{page.url.pathname || page.route.id}</a>
     </div>
</div>