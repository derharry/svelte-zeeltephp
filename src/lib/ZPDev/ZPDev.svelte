<script>
// ZPDev.svelte
     import './zpdev.css'

     import ZPDevNav             from "./ZPDevNav.svelte"
     import ZPDevResponseStates  from "./ZPDevResponseStates.svelte";
     import ZPDevAppPageServer   from "./ZPDevAppPageServer.svelte";
     import ZPDevAppServer       from "./ZPDevAppServer.svelte";
     import ZPDevAppDB           from "./ZPDevAppDB.svelte";

     import { zp_fetch } from "zeeltephp";
     import { 
          showApp,
          showDumpPanel, dataDumpPanel,
          promise_fetch, 
          init_ZPDev
     } from './zpdev.stores'

</script>

<form
     class="frameParent" 
     onsubmit={(e) => {
          init_ZPDev(e)
          $promise_fetch = zp_fetch(e)
     }}
>
     <!-- NAV -->
     <ZPDevNav />

     <!-- MAIN -->
     <div class="zpdev-tab-dump">

          <!-- Apps .tab -->
          <div class="contentPadding zpdev-apps svcolor-editor">
               <ZPDevResponseStates />
               <hr />
               {#if $showApp == "PAGE.SERVER.PHP"}
                    <ZPDevAppPageServer {init_ZPDev} />
               {:else if $showApp == "SERVER.PHP"}
                    <ZPDevAppServer {init_ZPDev} />
               {:else if $showApp == "DB"}
                    <ZPDevAppDB {init_ZPDev} />
               {:else}
                    <div>
                         newApp {$showApp}
                    </div>
               {/if}
               <!--
               {:else if $showApp == ".ENV"}
                    <div>
                         <button
                              type="submit"
                              name="btnEnvGet"
                              formaction="?/ENV_get"
                              style="width:50%"
                         >Get .ENV</button>
                    </div>
               {:else if $showApp == "ROUTES"}
                    <ZPDevAppRoutes />
               -->
          </div>

          <!-- DumpPanel .dump -->
          <div class="contentPadding zp-vardump svcolor-preview">
               <span>{$showDumpPanel}: {typeof $dataDumpPanel}</span>
               <pre style="overflow:auto">{JSON.stringify($dataDumpPanel, null, 2)}</pre>
          </div>
     </div>

     <!-- FOOTER -->

</form>