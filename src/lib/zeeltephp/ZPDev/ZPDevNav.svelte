<script>
     import './zpdev.css';
     import { zp_page_route }         from "../zp.tools.js";
     import { showApp, showDumpPanel, zpAR_php }     from "./zpdev.stores.js";
     import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
     import HTML_Marquee              from "$lib/zeelte/HTML_Marquee.svelte";
     
     let {
          promise_fetch = $bindable()
     } = $props();
 
</script>

     <div class="frameHeader svcolor-header">

          <span class="zp-title">
               ZP Dev
               {#await promise_fetch}
                    <HTML_Marquee value="🐘" />
               {:then _}
                    {#if zpAR_php && !zpAR_php?.routeFiles}
                         ⚠️
                    {:else}
                         🐘
                    {/if}
               {/await}
          </span>
     <!-- navButtons to show current App -->
          <div>
               <button
                    class="tab"
                    class:active={$showApp === "PAGE.SERVER.PHP"}
                    onclick={(e) => { e.preventDefault(); showApp.set("PAGE.SERVER.PHP")}}
               >+page.server.php</button>
               <button
                    class="tab"
                    class:active={$showApp === "SERVER.PHP"}
                    onclick={(e) => { e.preventDefault(); showApp.set("SERVER.PHP")}}
               >+server.php</button>
               <button
                    class="tab"
                    class:active={$showApp === "DB"}
                    onclick={(e) => { e.preventDefault(); showApp.set("DB")}}
               >DB</button>
               <button
                    class="tab"
                    class:active={$showApp === ".ENV"}
                    onclick={(e) => { e.preventDefault(); showApp.set(".ENV")}}
               >.ENV</button>
          </div>

          <div>
               <button
                    class="tab"
                    class:active={$showDumpPanel === "data"}
                    onclick={(e) => { e.preventDefault(); showDumpPanel.set("data")}}
               >data</button>
               <button
                    class="tab"
                    class:active={$showDumpPanel === "form"}
                    onclick={(e) => { e.preventDefault(); showDumpPanel.set("form"); }}
               >form</button>
               <button
                    class="tab"
                    class:active={$showDumpPanel === "error"}
                    onclick={(e) => { e.preventDefault(); showDumpPanel.set("error"); }}
               >error</button>
          </div>
          <!--
               <button
                    onclick={(e) => { e.preventDefault(); showDumpsInJSON = !showDumpsInJSON)}
                    class:active={showDumpsInJSON}
               >{#if showDumpsInJSON}JSON{:else}DUMP{/if}</button>
          -->
          <!--
               <button onclick={() => (showApp.set("DASHBOARD"))}  class:activeApp={showApp=="DASHBOARD"}>Dashboard</button>
          -->

          <a   href="{PUBLIC_ZEELTEPHP_BASE}?{zp_page_route()}"
               target="_blank"
               class="zp-route"
               onclick={ () => { return; /*showApp = "IFRAME"*/ } }
          >{zp_page_route()}</a>
          
     </div>