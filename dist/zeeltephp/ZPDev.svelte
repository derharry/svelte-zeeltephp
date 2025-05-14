<script lang="ts">

     // ZeeltePHP imports
     import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
     import { ZP_ApiRouter } from "../zeelte/class.zp.apirouter.js";
     import { ZP_EventDetails } from "../zeelte/class.zp.eventdetails.js";
     import { zp_fetch_api } from "../zeelte/zp.fetch.api.js";
     import { tinyid } from "../zeelte/tiny.id.js";
     import Loader from "./Loader.svelte";
     import VarDump from "../zeelte/VarDump.svelte";
     
     // svelte imports
     import { page } from '$app/state';
     import { invalidateAll } from "$app/navigation";
     import { base } from "$app/paths";
     import { onMount } from "svelte";

     import { zp_page_route } from "../zeelte/inc.zp.tools.js";

     export let data;
     export let form;

     let sv_route;
     let zp_route;
     let zp_action = 'action';
     let zp_value  = '42';
     let zp_testFormData = {
          name : tinyid(4), // crypto.randomUUID(),
          any  : tinyid(4), // crypto.randomUUID()
     }

     let dashboardState = {
          validPhpResponse : 0,
          phpFileFound     : 0,
          phpError         : 0,
          phpDataReceived  : 0,
     }


     let showApp  = "PAGE.SERVER.PHP" //"DASHBOARD"//"ROUTE"//"PAGE.SERVER.PHP";
     let promise_fetch;

     let showDumpsInJSON = false;
     let zpED_svelte;
     let zpAR_pageJS;
     let zpAR_svelte;
     let zpAR_php;
     let zpDB_php;

     const setVariablesToInit = () => {
          promise_fetch = undefined;
          zpAR_svelte = undefined;
          zpAR_pageJS = undefined;
          zpAR_php = undefined;
          zpED_svelte = undefined;
          data = undefined;
          dashboardState.validPhpResponse = 0;
          dashboardState.phpFileFound  = 0;
          dashboardState.phpError      = 0;
          dashboardState.phpDataReceived = 0;
          dashboardState = dashboardState;
     }
          
     const getIconState = (state = 0) => {
          const iconStates = [
               ' · ', //0 nothing
               '✅', //1 ok
               '❌', //2 error
               '⚠️', //3 unknown check
          ];
          if (iconStates[state])
               return iconStates[state];
          return '?'
     }


     const setDashboardStates = () => {
          // is valid response?
          // is valid zpAR response?
          // is +page.server.php found?
          // is error response?
          dashboardState.validPhpResponse = !data ? 0 : data?.ok || !data?.ok ? 1 : 2;
          dashboardState.phpFileFound     = !zpAR_php ? 0 : zpAR_php && zpAR_php?.routeFileExist  ? 1 : 3;
          dashboardState.phpError         = !zpAR_php ? 0 : data?.type ? 2 :  // general php error
                                                  !dashboardState.phpFileFound ? 3 : 1;
          dashboardState.phpDataReceived  = dashboardState.validPhpResponse == 1 && dashboardState.phpFileFound == 1 && !data?.type ? 1 : 0;
     }

     $: if (data) {
          // parse and destruct received data 
          // destruct the response so that ZPDev shows the correct data in its panels
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
          // now data should be real user dat (does Svelte triggers?)
          data = data;
          setDashboardStates();
     }

     /**
      * autoload
     */
     onMount(() => {
          // -- init_ZPDev(); // don't otherwhise we clear the console 
               //sv_route = page.url.pathname.replace(base, ''); 
               //zp_route = zp_page_route(); 
          // if data,form is used - check on it :-)
          //post_api_response(page.data);
          // invoke setDashboardStates to get basic checks.
          //setDashboardStates();
     });


     /**
      * before any fetching - reset the variables and view
      */
     function init_ZPDev(e = undefined) {
          console.clear(); // reset view as well
          setVariablesToInit();
          zpED_svelte = new ZP_EventDetails(e);
          zpAR_svelte = new ZP_ApiRouter(e);
     }


     /**
      * 
      */
     function handle_btnAction(e = null) {
          init_ZPDev(e);
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte)
          .then((datax) => { data = datax; })
          .catch((error) => {
               zpAR_php = error;
               console.error(error);
          });
     }

     /**
      * send form-data as default-data (JSON)
      * @param e
      */
     function handle_sendFormJson(e) {
          init_ZPDev(e);
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte, zp_testFormData, 'GET')
          .then((datax) => { data = datax; })
          .catch((error) => {
               zpAR_php = error;
               console.error(error);
          });
     }

     /**
      * send form-data as default-data (formData) 
      * @param e
      */
     function handle_submitForm(e) {
          init_ZPDev(e);
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte)
          .then((datax) => { data = datax; })
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
<style>* {
     /** reset all and set to minimum defaults */
     font-size: 0.99em;
     margin:  0;
     padding: 0;
     text-align: left;
}


.frameParent {
     /** frameParent, -docked and -content */
     position: relative;
     background-color: snow;
     border: 3px orange inset;
}


.frameDocked {
     padding: 0.25em 0.56em;
}


.frameContent {
     p_adding: 0.25em;
}


.frameContentPadding {
     padding: 0.25em;
}


.panel_zpARtop {
     margin-top: 0; /*0.33em;*/
     border-top: orange 1px solid !important;
}


.panel_zpARright {
     border-right: orange 1px solid !important;
}


section {
     background-color: bisque;
     margin: -0.25em; /*dock back by reverse spacing*/
     padding: 0.33em;
     max-width: 20em;
}


table, div {
     /* fill up space - use full width */
     width: 100% !important;
}


table td {
     /** default all td to show content starting from top */
     vertical-align: top;
     background-color: none !important;
}


hr {
     margin: 0.25em;
     border: none;
}


input {
     background-color: white !important;
     border:    1px black solid;
     border-radius: 0.25em;
     padding: 0 0.33em;
     width: auto;
}


button {
/* default style of all Buttons */
     background-color: lightblue;
     padding: 0em 0.5em 0.2em;
     vertical-align: center;
     border: gray 1px solid;
     border-radius: 0.25em;
     text-align: center;
}


.zpDevHeader button {
     background-color: #c1c1c1;
}


.zpDevHeader button .isActiveApp {
     background-color: orange !important;
}


.zpDevHeader {
     display: flex;            /** 1row */
     flex-direction: row;
     gap: 0.5em;
     align-items: center;      /** baseline */
     background: orange;
}


.zpDevHeader .zpDev_zp_route {
     background-color: darkgoldenrod;
     color: orange;
     padding: 0.05em 0.7em;
     border-radius: 1em;
}


.zpDevHeader .zpDevTitle {
}


.center { text-align: center;  }


.nowrap { white-space: nowrap; }


.maxWidth { width: max-content !important; }


.formDataButtons {
     display:flex; 
}


section .keyValueInput {
/* style of appKeyValueInput rows */
     display:flex; 
}
</style>

<div class="frameParent">
<div class="frameDocked zpDevHeader">
<!-- #panel Header NAV -->

          <span class="zpDevTitle">ZP Dev 
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
          <button on:click={() => (showApp = "PAGE.SERVER.PHP")} class:isActiveApp={showApp=="PAGE.SERVER.PHP"}>+page.server.php</button>
          <button on:click={() => (showApp = "ROUTE")}           class:isActiveApp={showApp=="ROUTE"}>Routes</button>
          <button on:click={() => (showDumpsInJSON = !showDumpsInJSON)} class:activeApp={showDumpsInJSON}>{#if showDumpsInJSON}JSON{:else}DUMP{/if}</button>
          
          <!--
          <button on:click={() => (showApp = "DASHBOARD")} class:activeApp={showApp=="DASHBOARD"}>Dashboard</button>
          <button on:click={() => (showApp = "SERVER.PHP")} class:activeApp={showApp=="APIPHP"}>+server.php</button>
          <button on:click={() => (showApp = "API.PHP")} class:activeApp={showApp=="APIPHP"}>+api.php</button>
          <button on:click={() => (showApp = "DB")} class:activeApp={showApp=="DB"}>DB</button>
          <a 
               href="{PUBLIC_ZEELTEPHP_BASE}?{zp_page_route()}" 
               target="_blank"
               class="zpDev_zp_route"
                
          >?{zpAR_svelte?.route || ''} vs {zp_page_route()}</a>
          -->
          <button on:click={() => (showApp = "IFRAMEPHP")}       class:isActiveApp={showApp=="IFRAMEPHP"} class="zpDev_zp_route">{zp_page_route()}</button>

<!-- /panel Header NAV -->
</div>
<div class="frameContent">


<table><tbody><tr>
<td width="33%" class="frameContentPadding">
<!-- #panel Apps -->

          <section>
               <table>
                    <tbody>
                         <tr>
                              <td class="center" width="20">{getIconState(dashboardState.validPhpResponse)}</td>
                              <td class="nowrap">api response <small>or +page.js</small></td>
                         </tr>
                         <tr>
                              <td class="center">{getIconState(dashboardState.phpFileFound)}</td>
                              <td>+.php route found?</td>
                         </tr>
                         <tr>
                              <td class="center">{getIconState(dashboardState.phpError)}</td>
                              <td>php error</td>
                         </tr>
                         <tr>
                              <td class="center">{getIconState(dashboardState.phpDataReceived)}</td>
                              <td>data received</td>
                         </tr>
                    </tbody>
               </table>
          </section>

          {#if showApp == "DASHBOARD"}
               // is now default outside above
          {:else if showApp == "ROUTE"}
               <section>
                    <table>
                    <tbody>
                         <tr>
                              <td>base</td>
                              <td class="nowrap">{base}</td>
                         </tr>
                         <tr>
                              <th width="1">zp_route (expected)</th>
                              <td class="nowrap">{zp_route}</td>
                         </tr>
                         <tr>
                              <th>zp_route (zpAR)</th>
                              <td class="nowrap">{zpAR_svelte?.route || ''}</td>
                         </tr>
                         <tr>
                              <th>sv_route</th>
                              <td class="nowrap">{sv_route}</td>
                         </tr>
                         <tr>
                              <th>page.url.pathname</th>
                              <td class="nowrap">{page.url.pathname}</td>
                         </tr>
                         <tr>
                              <th>page.url.search</th>
                              <td class="nowrap">{page.url.search}</td>
                         </tr>
                         <tr>
                              <th>page.route.id</th>
                              <td class="nowrap">{page.route.id}</td>
                         </tr>
                    </tbody>
                    </table>
               </section>
          {:else if showApp == "PAGE.SERVER.PHP"}
               <section>
                    <table>
                    <tbody>
                         <tr>
                              <td class="nowrap">

                                   <button name="(re)load" on:click={handle_btnAction}> load() </button>
                                   <button name="btnAction" value="{zp_value}" formaction="?/{zp_action}" on:click={handle_btnAction}> ?/{zp_action}() </button>
                                   <button 
                                        name="btnInvalidateAll" 
                                        value="{zp_value}" 
                                        formaction="?/{zp_action}" 
                                        on:click={async() => {
                                             init_ZPDev();
                                             promise_fetch = invalidateAll()
                                                  .catch(error => data = error)
                                                  .finally(() => {
                                                       //--setDashboardStates();
                                                       //--post_api_response(page.data);
                                                  })
                                        }} 
                                   > invalidateAll() </button>

                              </td>
                         </tr>
                         <tr>
                              <td class="keyValueInput">
                                   <table><thead><tr>
                                        <td width="20" class="center">?/</td>
                                        <td width="1"><input type="text" name="btnActionName"  bind:value={zp_action} size="10"/></td>
                                        <td width="20" class="center">=</td>
                                        <td><input type="text"  name="btnActionValue" bind:value={zp_value} size="6" /></td>
                                   </tr></thead></table>
                              </td>
                         </tr>
                         <tr>
                              <td><hr></td>
                         </tr>
                         <tr>
                              <td class="">
                                   <form on:submit={handle_submitForm}>
                                        <div class="formDataButtons">
                                             <button type="button" name="btnSendJson" on:click={handle_sendFormJson}  formaction="?/send_data_json" style="width:50%">send-json</button>
                                             <button type="submit" name="" formaction="?/submit_Form" style="width:50%">send-form</button>
                                        </div>
                                        <input type="text" name="name" bind:value={zp_testFormData.name} style=" width:100%" />
                                        <br>
                                        <input type="text" name="any"  bind:value={zp_testFormData.any}  style=" width:100%" />
                                   </form>
                              </td>
                         </tr>
                    </tbody>
                    </table>
               </section>
          {:else if showApp == "DB"}
               <section>
                    <table>
                    <tbody>
                         <tr>
                              <td>ZEELTEPHP_DATABASE_URL</td>
                              <td></td>
                         </tr>
                    </tbody>
                    </table>
               </section>
          {:else}
               <section>
                    newApp {showApp} (template)
                    <table>
                    <tbody>
                         <tr>
                              <td></td>
                              <td></td>
                         </tr>
                    </tbody>
                    </table>
               </section>
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
          {/if}

<!-- /panel APPS -->
</td>
<td class="zpDev_Data frameContentPadding">
<!-- #panel DATA -->

          <VarDump title="data" vardump={data} cssStyle="" dumpJson={showDumpsInJSON} />

          <!--
               {#await promise_fetch}
                    <Loader value=" ... fetching data ... " />
               {:then _} 
               {/await}
          -->

<!-- /panel DATA -->
</td>
</tr>
</tbody>
</table>

</div>


<!-- #panel zpAR left/right Svelte/PHP -->

     {#if showApp == 'IFRAMEPHP'}
          <iframe src={PUBLIC_ZEELTEPHP_BASE} style="width:100%; height: 25em"/>
     {:else}
          <table width="100%" class="panel_zpARtop">
          <tbody>
               <tr>
                    <td width="50%" class="frameContentPadding panel_zpARright"><VarDump title="zpAR svelte" vardump={zpAR_svelte}  dumpJson={showDumpsInJSON} /></td>
                    <td width="50%" class="frameContentPadding"><VarDump title="zpAR php"    vardump={zpAR_php}     dumpJson={showDumpsInJSON} /></td>
               </tr>
          </tbody>
          </table>
     {/if}
     
<!-- /panel zpAR left/right Svelte/PHP -->

</div>
