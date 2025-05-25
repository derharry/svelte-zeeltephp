<script lang="ts">
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
      */

     // ZeeltePHP imports
     import { tinyid } from "../zeelte/tiny.id.js";
     import { zp_page_route } from "../zeelte/zp.tools.js";
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

     // style import
     import "./zpdev.css";


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
     let showDumpsInJSON = false;

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
     <div class="frameHeader">

          <span class="zpDevTitle">
               <b>ZP Dev</b>
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
          <button
               on:click={() => (showApp = "PAGE.SERVER.PHP")}
               class:isActiveApp={showApp == "PAGE.SERVER.PHP"}
          >+page.server.php</button >
          <button
               on:click={() => (showApp = "DB")}
               class:isActiveApp={showApp == "DB"}
          >DB</button>
          <button
               on:click={() => (showApp = ".ENV")}
               class:isActiveApp={showApp == ".ENV"}
          >.ENV</button>
          <button
               on:click={() => (showApp = "ROUTES")}
               class:isActiveApp={showApp == "ROUTES"}
          >Routes</button>
          <button
               on:click={() => (showDumpsInJSON = !showDumpsInJSON)}
               class:isActiveApp={showDumpsInJSON}
          >{#if showDumpsInJSON}JSON{:else}DUMP{/if}</button>
          <a
               hr_ef="{PUBLIC_ZEELTEPHP_BASE}?{zp_page_route()}"
               on:click={() => (showApp = "IFRAME")}
               target="_blank"
               class="zpDev_zp_route"
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
 <table>
     <tbody>
     <tr>
          <td width="1%" style="width: 1% !important;">
               <section class="frameContentPadding">
                    <table><tbody>
                         <tr>
                              <td class="center" width="20">{@html getIconState(dashboardState.validPhpResponse) }</td>
                              <td class="nowrap">api response <small>php | +page.js</small></td>
                         </tr>                        
                         <tr>
                              <td class="center" width="20">{@html getIconState(dashboardState.phpFileFound) }</td>
                              <td class="nowrap">+.php route found?</td>
                         </tr>                         
                         <tr>
                              <td class="center" width="20">{@html getIconState(dashboardState.phpError) }</td>
                              <td>php error</td>
                         </tr>                         
                         <tr>
                              <td class="center" width="20">{@html getIconState(dashboardState.phpDataReceived) }</td>
                              <td>data received</td>
                         </tr>
                    </tbody></table>

                    <hr>
                    <!--
                         // https://github.com/l-portet/svelte-switch-case
                         -- not working ??
                         -- typescript required?
                         {#switch showApp}
                         {:case "cat"}
                         <p>meow</p>
                         {:case "dog"}
                         <p>woof</p>
                         {:default}
                         <p>oink?</p>
                         {/switch}
                    -->
                    {#if showApp == "DASHBOARD"}
                         // is now default outside above
                    {:else if showApp == "PAGE.SERVER.PHP"}
                         <table><tbody>
                              <tr>
                                   <td class="nowrap">
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
                                   </td>
                              </tr>
                              <tr>
                                   <td class="keyValueInput">
                                        <table><tbody>
                                             <tr>
                                                  <td width="1">?/</td>
                                                  <td><input type="text" name="btnActionName"  bind:value={zp_action} size="5" /></td>
                                                  <td width="1">=</td>
                                                  <td><input type="text" name="btnActionValue" bind:value={zp_value} size="5" /></td>
                                             </tr>
                                        </tbody></table>
                                   </td>
                              </tr>
                         </tbody></table>
                         <table><tbody>
                              <tr>
                                   <td colspan="2" class="nowrap">
                                        <button
                                             name=""
                                             type="submit"
                                             formaction="?/submit_Form"
                                             style="width:50%"
                                        >send-form</button>
                                        <button
                                             name="btnSendJson"
                                             type="button"
                                             formaction="?/send_data_json"
                                             style="width:50%"
                                             on:click={handle_sendFormAsJson}
                                        >send-json</button>
                                   </td>
                              </tr>
                              <tr>
                                   <td><input type="text" name="name" bind:value={ zp_data.name } size="5"/></td>
                                   <td><input type="text" name="any"  bind:value={ zp_data.any }  size="5" /></td>
                              </tr>
                         </tbody></table>
                    {:else if showApp == "DB"}
                         <table><tbody>
                              <tr>
                                   <td>
                                        <button
                                             type="submit"
                                             name="btnDBcheck"
                                             formaction="?/DB_get"
                                        >Get DB</button>
                                   </td>
                                   <td>
                                        <button
                                             type="submit"
                                             name="btnDBsqlst"
                                             formaction="?/DB_execSQL"
                                        >exec SQL</button>
                                   </td>
                              </tr>
                              <tr>
                                   <td colspan="2">
                                        <textarea name="sqlstatement" style="width:100%; min-height: 5em;">SELECT 'Hello,  World!' AS message;</textarea>
                                   </td>
                              </tr>
                         </tbody></table>
                    {:else if showApp == ".ENV"}
                         <table><tbody>
                              <tr>
                                   <td>
                                        <button
                                             type="submit"
                                             name="btnEnvGet"
                                             formaction="?/ENV_get"
                                             style="width:50%"
                                        >Get .ENV</button>
                                   </td>
                                   <td></td>
                              </tr>
                         </tbody></table>
                    {:else if showApp == "ROUTES"}
                         <table><tbody>
                              <tr>
                                   <td>base</td>
                                   <td class="nowrap">{base}</td>
                              </tr>
                              <tr>
                                   <td>sv_route</td>
                                   <td class="nowrap">{sv_route}</td>
                              </tr>
                              <tr>
                                   <td>zp_route <small>expected</small></td>
                                   <td class="nowrap">{zp_page_route()}</td>
                              </tr>
                              <tr>
                                   <td>zp_route <small>zpAR</small></td>
                                   <td class="nowrap">{zpAR_svelte?.route ||""}</td>
                              </tr>
                              <tr>
                                   <td>page.route.id</td>
                                   <td class="nowrap">{page.route.id}</td>
                              </tr>
                              <tr>
                                   <td>page.url.pathname</td>
                                   <td class="nowrap">{page.url.pathname}</td>
                              </tr>
                              <tr>
                                   <td>page.url.search</td>
                                   <td class="nowrap">{page.url.search}</td>
                              </tr>
                         </tbody></table>
                    {:else}
                         <table><tbody>
                              <tr>
                                   <td>newApp  (template)</td>
                                   <td>{showApp}</td>
                              </tr>
                         </tbody></table>
                    {/if}
               </section>
          </td>
          <td width="99%">
               <VarDump
                    title="data"
                    vardump={data}
                    dumpJson={showDumpsInJSON}
                    noBorder={true}
               />
          </td>
          
     </tr>
     </tbody>
 </table>


<!-- Content ZPAR Svelte / PHP -->
     {#if showApp == "IFRAME"}
          <iframe
               src={PUBLIC_ZEELTEPHP_BASE}
               style="width:100%; height: 15em; background-color:darkslategray"
          />
     {:else}
          <div style="display: flex; width: 100%; gap: 0px; margin-top:0px">
               <div style="flex: 1;" class="frameContentPadding">
                    <VarDump
                         title="zpAR svelte"
                         vardump={zpAR_svelte}
                         dumpJson={showDumpsInJSON}
                         noBorder={true}
                    />
               </div>
               <div style="flex: 1;" class="frameContentPadding">
                    <VarDump
                         title="zpAR php"
                         vardump={zpAR_php}
                         dumpJson={showDumpsInJSON}
                         noBorder={true}
                    />
               </div>
          </div>
     {/if}

</form>