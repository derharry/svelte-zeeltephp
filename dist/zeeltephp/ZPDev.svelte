<script>
     
     /**
      * ZPDev Svelte Component
      *
      * Developer dashboard and playground for ZeeltePHP integration.
      * Demonstrates API calls, action handling, form submissions, and state debugging.
      *
      * Dependencies:
      *  - ZeeltePHP API and tools
      *  - SvelteKit navigation and state
      *  - Loader and VarDump Svelte components for UI/UX
      * 
      * 2025.05.25 - VarDump.svelte, show showDumpsInJSON currently not in use.
      */
     // ZeeltePHP imports
     import { tinyid } from "../zeelte/tiny.id.js";
     import { zp_page_route } from "./zp.tools.js";
     import { zp_fetch_api } from "../zeelte/zp.fetch.api.js";
     import { ZP_ApiRouter } from "../zeelte/class.zp.apirouter.js";
     import { ZP_EventDetails } from "../zeelte/class.zp.eventdetails.js";
     import Loader from "../zeelte/Loader.svelte";
     import VarDump from "../zeelte/VarDump.svelte";
     import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
     //const PUBLIC_ZEELTEPHP_BASE = import.meta.env.PUBLIC_ZEELTEPHP_BASE;

     // svelte imports
     import { base } from "$app/paths";
     import { page } from "$app/state";
     import { invalidateAll } from "$app/navigation";
     import { onMount } from "svelte";

     // Props from parent +page.js
     export let data;
     export let form;

     /** Promise for API fetch (for Svelte's #await) */
     let promise_fetch;

     /** SvelteKit route path */
     let sv_route;

     /** ZeeltePHP route path */
     let zp_route;

     /** Action name for API testing */
     let zp_action = "action";

     /** Action value for API testing */
     let zp_value = "42";

     /** Toggle for showing dumps as JSON */
     let showDumpsInJSON = true;

     /** Which app panel to show DASHBOARD|ROUTE|DB|... */
     let showApp = "PAGE.SERVER.PHP"; //;

     /** Example data payload for API calls */
     let zp_data = {
          name: tinyid(4),
          any: tinyid(4),
     };

     /** Dashboard state for UI status indicators */
     let dashboardState = {
          validPhpResponse: 0,
          phpFileFound: 0,
          phpError: 0,
          phpDataReceived: 0,
     };


     /** ZeeltePHP event and router objects for Svelte and PHP sides */
     let zpED_svelte;
     let zpAR_pageJS;
     let zpAR_svelte;
     let zpAR_php;
     let zpDB_php;

     /**
      * Resets all dashboard and API state variables to initial values.
      */
     const setVariablesToInit = () => {
          promise_fetch = undefined;
          zpAR_svelte = undefined;
          zpAR_pageJS = undefined;
          zpAR_php = undefined;
          zpED_svelte = undefined;
          data = undefined;
          dashboardState.validPhpResponse = 0;
          dashboardState.phpFileFound = 0;
          dashboardState.phpError = 0;
          dashboardState.phpDataReceived = 0;
          dashboardState = dashboardState;
     };

     /**
      * Returns a status icon based on the state code.
      * @param state Status code (0–3)
      * @returns {string} Emoji or symbol
      */
     const getIconState = (state = 0) => {
          const iconStates = [
               " · ", //0 nothing
               "✅", //1 ok
               "❌", //2 error
               "⚠️", //3 unknown check
          ];
          if (iconStates[state]) return iconStates[state];
          return "?";
     };

     /**
      * Sets dashboard state indicators based on the latest API response and router info.
      */
     const setDashboardStates = () => {
          // is valid response?
          // is valid zpAR response?
          // is +page.server.php found?
          // is error response?
          dashboardState.validPhpResponse = !data ? 0 : data?.ok || !data?.ok ? 1 : 2;
          dashboardState.phpFileFound     = !zpAR_php ? 0 : zpAR_php?.routeFileExist ? 1 : 3;
          dashboardState.phpError         = !zpAR_php ? 0 : data?.type ? 2 : (!dashboardState.phpFileFound ? 3 : 1);
          dashboardState.phpDataReceived  = dashboardState.validPhpResponse === 1 && dashboardState.phpFileFound === 1 && !data?.type ? 1 : 0;
     }

     /** Reactively update dashboard state and destructure API response */
     $: if (data) {
          if (data?.zpAR) {
               zpAR_php = data.zpAR;
               delete data.zpAR;
          }
          if (data?.zpDB) {
               zpDB_php = data.zpDB;
               delete data.zpDB;
          }
          if (data?.zpENV) {
               zpDB_php = data.zpDB;
               delete data.zpDB;
          }
          if (data?.zpAR_pageJS) {
               zpAR_pageJS = data.zpAR_pageJS;
               delete data.zpAR_pageJS;
          }
          data = data;
          setDashboardStates();
     }

     /**
      * Can be used for initial dashboard setup or auto-fetching.
      */
     onMount(() => {
          // -- init_ZPDev(); // don't otherwhise we clear the console
          // Optionally initialize dashboard or fetch data here
          // setDashboardStates();
     });

     /**
      * Initializes dashboard and API state before a fetch.
      * @param e Optional event object
      */
     function init_ZPDev(e = undefined) {
          console.clear();
          setVariablesToInit();
          zpED_svelte = new ZP_EventDetails(e);
          zpAR_svelte = new ZP_ApiRouter(e);
     }

     /**
      * Handles button actions for API calls (load, action, invalidateAll).
      * @param e Event object
      */
     function handle_btnAction(e = null) {
          e.preventDefault();
          init_ZPDev(e);
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte)
               .then((dataX) => { data = dataX; })
               .catch((error) => {
                    zpAR_php = error;
                    console.error(error);
               });
     }

     /**
      * Submits form data as FormData to the backend.
      * @param e Event object
      */
     function handle_submitForm(e) {
          init_ZPDev(e);
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte)
               .then((datax) => {
                    data = datax;
               })
               .catch((error) => {
                    zpAR_php = error;
                    console.error(error);
               });
     }
     
     /**
      * Sends form data as JSON to the backend.
      * @param e Event object
      */
     function handle_sendFormAsJson(e) {
          init_ZPDev(e);
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte, zp_data, "POST")
               .then((dataX) => { data = dataX; })
               .catch((error) => {
                    zpAR_php = error;
                    console.error(error);
               });
     }

     /*
     function getSvelteStates() {
          // is there data in $page.data?
          // init check if there is data from +page.js?
          // for now use <ZPDev {data} {form} />
          // -- data = page.data;
          // -- form = page.form;
     }
     function setSvelteStates() {
          /**
          * Trigger Svelte 
          * SvelteKit works out of the box, can we trigger some things of Svelte to further automate responses and simulate SvelteKit?
          * If that works - zp_fetch_api()  clould do the final steps to automate the response back and have some SvelteKits magic back.
          * /
     }
     */
</script>

<form class="frameParent" on:submit={handle_submitForm}>

<!-- Header NAV -->
     <div class="frameHeader svcolor-header">

          <span class="zp-title">
               ZP Dev
               {#await promise_fetch}
                    <Loader value="🐘" />
               {:then _}
                    {#if zpAR_php && !zpAR_php?.routeFileExist}
                         ⚠️
                    {:else}
                         🐘
                    {/if}
               {/await}
          </span>
     <!-- navButtons to show current App -->
          <a href=""
               class="tab"
               class:active={showApp === "PAGE.SERVER.PHP"}
               on:click={() => (showApp = "PAGE.SERVER.PHP")}
          >+page.server.php</a>
          <a href=""

               class="tab"
               class:active={showApp === "DB"}
               on:click={() => (showApp = "DB")}
          >DB</a>
          <a href=""
               class="tab"
               class:active={showApp == ".ENV"}
               on:click={() => (showApp = ".ENV")}
          >.ENV</a>
          <!--
          <a href=""
               class="tab"
               class:active={showApp == "ROUTES"}
               on:click={() => (showApp = "ROUTES")}
          >Routes</a>
          <button
               on:click={() => (showDumpsInJSON = !showDumpsInJSON)}
               class:active={showDumpsInJSON}
          >{#if showDumpsInJSON}JSON{:else}DUMP{/if}</button>
          -->
          <a
               href="{PUBLIC_ZEELTEPHP_BASE}?{zp_page_route()}"
               target="_blank"
               class="zp-route"
               on:click={ () => { return; showApp = "IFRAME"} }
          >{zp_page_route()}</a>
          <!--
               <button on:click={() => (showApp = "DASHBOARD")}  class:activeApp={showApp=="DASHBOARD"}>Dashboard</button>
               <button on:click={() => (showApp = "SERVER.PHP")} class:activeApp={showApp=="APIPHP"}>+server.php</button>
               <button on:click={() => (showApp = "API.PHP")}    class:activeApp={showApp=="APIPHP"}>+api.php</button>
               <button on:click={() => (showApp = "IFRAMEPHP")}  class:isActiveApp={showApp=="IFRAMEPHP"} class="zpDev_zp_route">{zp_page_route()}</button>
          -->
     </div>
<!-- // Header NAV -->


<!-- Content Apps / Data -->
     <div class="frameContent">
          <div class="zpdev-tab-dump">
               <div class="contentPadding zpdev-apps svcolor-editor">
                    <ul class="status-list">
                         <li>
                              <span class="icon">{@html getIconState(dashboardState.validPhpResponse)}</span>
                              <span class="desc">api response <small>php | +page.js</small></span>
                         </li>
                         <li>
                              <span class="icon">{@html getIconState(dashboardState.phpFileFound)}</span>
                              <span class="desc">+.php route found?</span>
                         </li>
                         <li>
                              <span class="icon">{@html getIconState(dashboardState.phpError)}</span>
                              <span class="desc">php error</span>
                         </li>
                         <li>
                              <span class="icon">{@html getIconState(dashboardState.phpDataReceived)}</span>
                              <span class="desc">data received</span>
                         </li>
                    </ul>
                    <hr>
                    {#if showApp == "DASHBOARD"}
                         // is now default outside above
                    {:else if showApp == "PAGE.SERVER.PHP"}
                         <div class="">
                              <button
                                   name="(re)load"
                                   on:click={handle_btnAction}
                              >load()</button>
                              <button
                                   name="btnAction"
                                   value={zp_value}
                                   formaction="?/{zp_action}"
                                   on:click={handle_btnAction}
                              >?/{zp_action}()</button>
                              <button
                                   name="btnInvalidateAll"
                                   value={zp_value}
                                   formaction="?/{zp_action}"
                                   on:click={async () => {
                                        init_ZPDev();
                                        promise_fetch = invalidateAll()
                                        .catch((error) => (data =  error))
                                        .finally(() => { /*--setDashboardStates();//--post_api_response(page.data);*/ },);
                                   }}
                              >invalidateAll()</button>
                         </div>
                         <div class="input-row">
                              <span>?/</span>
                              <input type="text" name="btnActionName"  bind:value={zp_action}  />
                              <span>=</span>
                              <input type="text" name="btnActionValue" bind:value={zp_value}  />
                         </div>
                         <hr>
                         <div class="input-row">
                              <button
                                   type="submit"
                                   name="btnSubmit"
                                   formaction="?/submit_Form"
                                   style="width:50%"
                              >send-form</button>
                              <button
                                   type="button"
                                   name="btnSendJson"
                                   formaction="?/send_data_json"
                                   style="width:50%"
                                   on:click={handle_sendFormAsJson}
                              >send-json</button>
                         </div>
                         <div class="input-row">
                              <input type="text" name="name" bind:value={ zp_data.name } />
                              <input type="text" name="any"  bind:value={ zp_data.any }  />
                         </div>
                    {:else if showApp == "DB"}
                         <div class="input-row">
                              <button
                                   type="submit"
                                   name="btnDBcheck"
                                   formaction="?/DB_get"
                              >Get DB</button>
                              <button
                                   type="submit"
                                   name="btnDBsqlst"
                                   formaction="?/DB_execSQL"
                              >exec SQL</button>
                         </div>
                         <textarea name="sqlstatement">SELECT 'Hello,  World!' AS message;</textarea>
                    {:else if showApp == ".ENV"}
                         <div>
                              <button
                                   type="submit"
                                   name="btnEnvGet"
                                   formaction="?/ENV_get"
                                   style="width:50%"
                              >Get .ENV</button>
                         </div>
                    {:else if showApp == "ROUTES"}
                         <dl class="kv-list">
                              <dt>base</dt>
                              <dd>{base}</dd>
                              <dt>sv_route</dt>
                              <dd>{sv_route}</dd>
                              <dt>zp_route <small>expected</small></dt>
                              <dd>{zp_page_route()}</dd>
                              <dt>zp_route <small>zpAR</small></dt>
                              <dd>{zpAR_svelte?.route || ""}</dd>
                              <dt>page.route.id</dt>
                              <dd>{page.route.id}</dd>
                              <dt>page.url.pathname</dt>
                              <dd>{page.url.pathname}</dd>
                              <dt>page.url.search</dt>
                              <dd>{page.url.search}</dd>
                         </dl>
                    {:else}
                         <div>
                              newApp {showApp}
                         </div>
                    {/if}
               </div>
               <div class="contentPadding zp-vardump svcolor-preview">
                    <span>data: {typeof data}</span>
                    <pre style="overflow:auto">{JSON.stringify(data, null, 2)}</pre>
               </div>
          </div>
     </div>

     <div class="zpdev-dump-dump frameFooter">
          <div class="contentPadding zp-vardump svcolor-preview">
               <span>zpAR svelte: {typeof zpAR_svelte}</span>
               <pre style="overflow:auto">{JSON.stringify(zpAR_svelte, null, 2)}</pre>
          </div>
          <div class="contentPadding zp-vardump svcolor-preview">
               <span>zpAR svelte: {typeof zpAR_php}</span>
               <pre style="overflow:auto">{JSON.stringify(zpAR_php, null, 2)}</pre>
               <!--
               <VarDump
                    title="zpAR php"
                    vardump={zpAR_php}
                    dumpJson={showDumpsInJSON}
                    noBorder={true}
               />
               -->
          </div>
     </div>

</form>


<style>

     .frameParent * {
          padding: 0;
          margin:  0;
          box-sizing: border-box;
          font-family: arial;
          font-size: 0.97em;
     }

     .frameParent {
          width: auto;
          border-radius: 0.5em;
          border: 4px #FF3E00 inset;
          background-color: #FF3E00 !important;
     }
     .frameHeader {
          display: flex !important;            /** 1row */
          flex-direction: row;
          gap: 0.25em;
          padding: 0.25em 0.5em;
          border-radius: 0.25em 0.25em 0 0;
          align-items: center !important; 
     }
     .frameFooter {
          margin-top: 1px;
          padding: 0em;
          border-radius: 0 0 0.25em 0.25em !important;
     }
     .frameContent {
          margin-top: 1px;
          padding: 0;
     }
     .contentPadding {
          padding: 0.25em !important;
     }


     .zpdev-tab-dump {
          display: grid;
          gap: 1px;
          grid-template-columns: minmax(10em, 17em) minmax(0, 1fr);
          width: 100%;

     }

     .zpdev-dump-dump {
          display: grid;
          grid-template-columns: auto, auto;
          display: flex;
          gap: 1px; /* optional, for spacing between columns */
     }
     .zpdev-dump-dump > div {
          flex: 1 1 0;
          min-width: 0; /* important for word breaking in flex items */
          border-radius: 0 0 0.33em 0.33em !important;
     }

     .zpdev-apps,
     .zpdev-apps * {
          color: #C4C9D0 !important; 
     }
     .zpdev-apps button {
          background-color: lightskyblue;
          color: black !important;
          font-style: italic;
          margin: 0;
     }

     .zp-vardump * {
          font-size: 0.96em !important;
     }
     .zp-vardump span {
          color: gray !important;
          font-style: italic;
     }
     .zp-vardump pre {
          overflow-wrap: break-word;
          word-break: break-all;
          hyphens: auto;
          overflow: no-scroll;
     }

     a {
          text-decoration: none;
     }
     a:hover {
          text-decoration: underline;
     }

     .tab {
          color: #D3D4D5;
          background: none;
          padding: 0em 0.25em !important;
          border: none !important;
     }
     .tab.active {
          color: #F96743;
     }
     .tab:hover {
          color: orange !important;
          text-decoration: none;
     }


     .zp-title {
          font-size: 0.95em;
          color: #F96743;
     }

     .zp-route {
          color: #D43008;
          font-size: 0.95em;
          background-color:rgb(60, 66, 77);
          padding: 0.05em 0.75em !important;
          margin-left: 1em;
          border-radius: 1em;
          text-decoration: none;
     }
     .zp-route:hover {
          background-color: #CACBCE;

     }


     .svcolor-header {
          color: white !important;
          background-color: #23272F !important;
     }

     .svcolor-editor {
          color: blue;
          background-color: #1E2235 !important; /*2E2E2E*/
     }

     .svcolor-preview,
     .svcolor-preview * {
          color: white !important;
          background-color: #2E2E2E !important;
     }


     HR {
          border: none;
          margin: 0.33em !important;
     }

     BUTTON {
          padding: 0 0.25em !important;
     }

     INPUT, 
     TEXTAREA {
          background-color: darkslategray !important;
          display: flex;
          flex: 1 1 0 !important;
          min-width: 0 !important;
          box-sizing: border-box !important;
          max-width: 100%;
          padding: 0.1em 0.25em !important;
          margin-top: 2px !important;
     }
     INPUT {
          height: fit-content !important;
          max-height: fit-content !important;
     }
     TEXTAREA {
          min-height: 5em;
          width: 100% !important;
     }

     .input-row {
          display: flex;
          align-items: center;
          width: 100%;
          gap: 0.25em;
     }
     .input-row span {
          flex: 0 0 auto;
     }

     .status-list {
          list-style: none;
          padding: 0;
          margin:  0;
     }
     .status-list li {
          display: flex;
          align-items: center;
          padding: 0.0em 0;
     }
     .status-list .icon {
          width: 20px;
          display: flex;
          justify-content: center;
          align-items: center;
          flex-shrink: 0;
     }
     .status-list .desc {
          margin-left: 0.5em;
          white-space: nowrap;
     }


     .kv-list {
          display: grid;
          grid-template-columns: auto 1fr; /* key, value */
          gap: 0.3em 1em; /* row gap, column gap */
          align-items: start;
          width: 100%;
          margin: 0;
          padding: 0;
     }

     .kv-list dt {
          font-weight: bold;
          color: #C4C9D0;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
     }

     .kv-list dd {
          margin: 0;
          color: #fff;
          overflow-wrap: break-word;
          word-break: break-all;
          min-width: 0;
     }

</style>
