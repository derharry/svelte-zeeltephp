<script lang="ts">

     // ZeeltePHP imports
     import './zpdev.css';
     import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
     import { ZP_ApiRouter } from "$lib/zeelte/class.zp.apirouter.js";
     import { ZP_EventDetails } from "../zeelte/class.zp.eventdetails.js";
     import { zp_fetch_api } from "$lib/zeelte/zp.fetch.api.js";
     import { zp_page_route } from "$lib/zeelte/inc.zp.tools.js";
     import Loader from "$lib/zeeltephp/Loader.svelte";
     import VarDump from "$lib/zeelte/VarDump.svelte";

     // svelte imports
     import { base } from "$app/paths";
     import { page } from '$app/state';
     import { onMount } from "svelte";
     import { invalidateAll } from "$app/navigation";

     let sv_route;
     let zp_route;
     export let data;
     export let form;

     let showApp  = "PAGE.SERVER.PHP" //"DASHBOARD"//"ROUTE"//"PAGE.SERVER.PHP";
     let promise_fetch;
     let zpED_svelte;
     let zpAR_pageJS;
     let zpAR_svelte;
     let zpAR_php;
     let zpDB_php;


     /**
      * before any fetching - reset the variables and view
      */
     function reset_ZPdev_toDefaults() {
          console.clear(); // reset view as well
          promise_fetch = undefined;
          zpAR_svelte = undefined;
          zpAR_php = undefined;
          zpED_svelte = undefined;
          data = undefined;
          dashboardState.validPhpResponse = 0;
          dashboardState.phpFileFound  = 0;
          dashboardState.phpError      = 0;
          dashboardState.phpDataReceived = 0;
          dashboardState = dashboardState;
     }

     /**
      * init ZP-svelte part
      * @param event
      */
     function init_ZP(e = null) {
          zpED_svelte = new ZP_EventDetails(e);
          zpAR_svelte = new ZP_ApiRouter(e);
     } 

     /**
      * destruct the response so that ZPDev shows the correct data in its panels
      * @param datax
      */
     function post_api_response(datax) {
          if (datax?.zpAR) {
               zpAR_php = datax.zpAR;
               delete datax.zpAR;
          }
          if (datax?.zpDB) {
               zpDB_php = datax.zpDB;
               delete datax.zpDB;
          }
          if (datax?.zpENV) {
               zpDB_php = datax.zpDB;
               delete datax.zpDB;
          }
          // now data should be real user dat (does Svelte triggers?)
          data = datax;
          get_basic_states();
     }

     /**
      * autoload
     */
     onMount(() => {
          init_ZP();
               //sv_route = page.url.pathname.replace(base, ''); 
               //zp_route = zp_page_route(); 
          // if data,form is used - check on it :-)
          post_api_response(page.data);
          // invoke get_basic_states to get basic checks.
          get_basic_states();
     });

function getSvelteStates() {
          // is there data in $page.data?
          // init check if there is data from +page.js?
          // for now use <ZPDev {data} {form} />
          // -- data = page.data;
          // -- form = page.form;
     }

/**
     * Trigger Svelte 
     * SvelteKit works out of the box, can we trigger some things of Svelte to further automate responses and simulate SvelteKit?
     * If that works - zp_fetch_api()  clould do the final steps to automate the response back and have some SvelteKits magic back.
     */
function setSvelteStates() {}

     //#region dasboard states
          let dashboardState = {
               validPhpResponse : 0,
               phpFileFound     : 0,
               phpError         : 0,
               phpDataReceived  : 0,
          }

          function getIconState(state) {
               const icons = [
                    ' ', //0 nothing
                    '✅', //1 ok
                    '❌', //2 error
                    '⚠️', //3 unknown check
               
               ];
               if (icons[state])
                    return icons[state];
               return '?'
          }

          function get_basic_states() {
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

     //#endregion

     //#region actions +page.server.php

          /**
           * 
           */
          function load(e = null) {
               reset_ZPdev_toDefaults();
               init_ZP(e);
               promise_fetch = zp_fetch_api(fetch, zpAR_svelte)
                    .then((datax) => {
                         post_api_response(datax);
                    })
                    .catch((error) => {
                         zpAR_php = error;
                         console.error(error);
                    });
          }

          let zp_action = 'action';
          let zp_value  = '42';
          let zp_testFormData = {
               name : crypto.randomUUID(),
               any  : crypto.randomUUID()
          }

          /**
           * customize ?/action
           * @param e
           */
          function btnAction(e) {
               reset_ZPdev_toDefaults();          
               init_ZP(e);
               console.log('btnAction()');
               promise_fetch = zp_fetch_api(fetch, zpAR_svelte)
                    .then((datax) => {
                         post_api_response(datax);
                    })
                    .catch((error) => {
                         zpAR_php = error;
                         console.error(error);
                    });
          }

          /**
           * send form-data as default-data (JSON)
           * @param e
           */
          function btnSendData(e) {
               reset_ZPdev_toDefaults();
               init_ZP(e);
               console.log('btnSendData()');
               promise_fetch = zp_fetch_api(fetch, zpAR_svelte, zp_testFormData)
                    .then((datax) => {
                         post_api_response(datax);
                    })
                    .catch((error) => {
                         zpAR_php = error;
                         console.error(error);
                    });
          }

          /**
           * send form-data as default-data (formData) 
           * @param e
           */
          function submit_Form(e) {
               reset_ZPdev_toDefaults();
               init_ZP(e);
               console.log('submit_Form()');
               promise_fetch = zp_fetch_api(fetch, zpAR_svelte, zp_testFormData)
                    .then((datax) => {
                         post_api_response(datax);
                    })
                    .catch((error) => {
                         zpAR_php = error;
                         console.error(error);
                    });
          }


     //#endregion

</script>

<div class="zpDevComponent">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
     <thead>
          <tr>
               <td colspan="2" class="header">
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
                    <nav>
                         <button on:click={() => (showApp = "PAGE.SERVER.PHP")} class:activeApp={showApp=="PAGE.SERVER.PHP"}>+page.server.php</button>
                         <button on:click={() => (showApp = "ROUTE")}     class:activeApp={showApp=="ROUTE"}>Routes</button>
                         <!--
                         <button on:click={() => (showApp = "DASHBOARD")} class:activeApp={showApp=="DASHBOARD"}>Dashboard</button>
                         <button on:click={() => (showApp = "SERVER.PHP")} class:activeApp={showApp=="APIPHP"}>+server.php</button>
                         <button on:click={() => (showApp = "API.PHP")} class:activeApp={showApp=="APIPHP"}>+api.php</button>
                         <button on:click={() => (showApp = "DB")} class:activeApp={showApp=="DB"}>DB</button>
                         -->
                         
                    </nav>
                    <a href="{PUBLIC_ZEELTEPHP_BASE}?{zp_page_route()}" target="_blank">?{zpAR_svelte?.route || ''} vs {zp_page_route()}</a>
               </td>
          </tr>
     </thead>
     <tbody>
          <tr>
               <td width="1%">
                         <section>
                              <table>
                                   <tbody>
                                        <tr>
                                             <td colspan="2">
                                             </td>
                                        </tr>
                                        <tr>
                                             <td width="35">{getIconState(dashboardState.validPhpResponse)}</td>
                                             <td class="nowrap">api response <small>or +page.js</small></td>
                                        </tr>
                                        <tr>
                                             <td>{getIconState(dashboardState.phpFileFound)}</td>
                                             <td>+.php route found?</td>
                                        </tr>
                                        <tr>
                                             <td>{getIconState(dashboardState.phpError)}</td>
                                             <td>php error</td>
                                        </tr>
                                        <tr>
                                             <td>{getIconState(dashboardState.phpDataReceived)}</td>
                                             <td>data received</td>
                                        </tr>
                                   </tbody>
                              </table>
                         </section>
                    {#if showApp == "DASHBOARD"}
                         // is now default see above
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
                                             <td class="dashSpacer nowrap">
                                                  <button name="(re)load" on:click={load}> load() </button>
                                                  <button name="btnAction" value="{zp_value}" formaction="?/{zp_action}" on:click={btnAction}> ?/{zp_action}() </button>
                                                  <button 
                                                       name="btnInvalidateAll" 
                                                       value="{zp_value}" 
                                                       formaction="?/{zp_action}" 
                                                       on:click={async() => {
                                                            reset_ZPdev_toDefaults();
                                                            promise_fetch = invalidateAll()
                                                                 .catch(error => console.error('error at invalidateAll()', error))
                                                                 .finally(() => {
                                                                      get_basic_states();
                                                                      post_api_response(page.data);
                                                                      init_ZP();
                                                                 })
                                                       }} 
                                                  > invalidateAll() </button>
                                             </td>
                                        </tr>
                                        <tr>
                                             <td class="keyValueInput">
                                                  ?/<input type="text" name="btnActionName"  bind:value={zp_action} />
                                                  =<input type="text"  name="btnActionValue" bind:value={zp_value}  />
                                             </td>
                                        </tr>
                                        <tr>
                                             <td class="dashSpacer">
                                                  <form on:submit={submit_Form}>
                                                       <div class="formButtonsJsonForm">
                                                            <button type="button" name="btnSendJson" on:click={btnSendData}  formaction="?/send_data_json" style="width:50%">send-json</button>
                                                            <button type="submit" name="" formaction="?/submit_Form" style="width:50%">send-form</button>
                                                       </div>
                                                       <input type="text" name="name" bind:value={zp_testFormData.name} />
                                                       <input type="text" name="any"  bind:value={zp_testFormData.any} />
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
               </td>
               <td style="vertical-align:top">
                    <VarDump title="data" vardump={data} cssStyle="background-color:none" />
                    <!--
                    {#await promise_fetch}
                         <Loader value=" ... fetching data ... " />
                    {:then _} 
                    {/await}
                    -->
               </td>
          </tr>
          <tr>
               <td colspan="2">

                    <table>
                         <tbody>
                              <tr class="top">
                                   <td><VarDump title="zpAR svelte" vardump={zpAR_svelte} /></td>
                                   <td><VarDump title="zpAR php" vardump={zpAR_php} /></td>
                              </tr>
                         </tbody>
                    </table>
               </td>     
          </tr>       
     </tbody>
</table>
</div>
