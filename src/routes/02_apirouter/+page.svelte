<script>
      import { page } from "$app/state";
      import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
      import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
      import { ZP_EventDetails } from "$lib/zeeltephp/class.zp.eventdetails";
      import {
            get_typeof,
            is_typeof,
            var_dump,
      } from "$lib/zeeltephp/dev.types";
      import VarDump from "$lib/VarDump.svelte";
      import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";

      export let zpAR_Svelte = new ZP_ApiRouter(); // {}
      export let zpAR_zpPHP = {};

      let vardump;

      function handle_typing(e) {
            const ed   = new ZP_EventDetails(e);
            const elemName  = ed.srcElement.name  || '';
            const elemValue = ed.srcElement.value || '';
            if (elemName == 'action' || elemName == 'value' || elemName == 'params') {
                  zpAR_Svelte[elemName] = elemValue;
                  zpAR_Svelte.prepare()
            }
            else if (ed.srcElement.name == 'urlRouteString') {
                  console.log('matchzz', ed.srcElement)
            }
      }

      async function handle_load(e) {
            try {
                  console.clear();
                  console.log("handle_click()");
                  vardump    = null;
                  zpAR_zpPHP = null;
                  zpAR_zpPHP = await zp_fetch_api(fetch);
                  vardump    = zpAR_zpPHP.data || zpAR_zpPHP;
                  zpAR_zpPHP = zpAR_zpPHP.zpAR;
            } catch (error) {
                  console.log(error);
            }
      }

      async function handle_zpEventDetails(e) {
            try {
                  console.clear();
                  console.log("handle_click()");
                  vardump    = null;
                  zpAR_zpPHP = null;
                  zpAR_zpPHP = await zp_fetch_api(fetch, e);
                  vardump    = zpAR_zpPHP.data || zpAR_zpPHP;
                  zpAR_zpPHP = zpAR_zpPHP.zpAR;
            } catch (error) {
                  console.log(error);
            }
      }

      function handle_urlRouteString(e) {
            try {
                  console.clear();
                  //vardump = new ZP_EventDetails(e);
                  vardump = {
                        id: 0,
                        name: "harry",
                        options: {
                              age: 41,
                              length: 176,
                              weight: 69,
                        },
                        events: [
                              {
                                    id: 0,
                                    title: "xxx",
                                    message: "Whomp",
                                    data: [],
                              },
                              { id: 1, title: "yyy", message: "Cat", data: [] },
                        ],
                  };
                  console.log("handle_urlRouteString()", e);
                  //vardump = e;
            } catch (error) {
                  console.log(error);
            }
      }


</script>

<h1>API Router / Test your Query</h1>

<table class="tableCompact">
      <tbody>
            <tr>
                  <td class="td50 defaultPadding">
                        <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
                        <form on:keydown={handle_typing}>
                        <table>
                              <tbody>
                                    <tr>
                                          <td width="1">ZeeltePHP_Base</td>
                                          <td>{PUBLIC_ZEELTEPHP_BASE}</td>
                                    </tr>
                                    <tr>
                                          <td width="1">zp_route</td>
                                          <td>{page.url.pathname}</td>
                                    </tr>
                                    <tr>
                                          <td>zp_action</td>
                                          <td class="zpFlexRow">
                                                <input
                                                      type="text"
                                                      name="action"
                                                      bind:value={
                                                            zpAR_Svelte.action
                                                      }
                                                />
                                                <input
                                                      type="text"
                                                      name="value"
                                                      bind:value={
                                                            zpAR_Svelte.value
                                                      }
                                                />
                                          </td>
                                    </tr>
                                    <tr>
                                          <td>zp_data/params</td>
                                          <td
                                                ><input
                                                      type="text"
                                                      name="params"
                                                      bind:value={
                                                            zpAR_Svelte.params
                                                      }
                                                /></td
                                          >
                                    </tr>
                                    <tr>
                                          <td>urlRoute</td>
                                          <td
                                                ><input
                                                      type="text"
                                                      name="urlRouteString"
                                                      bind:value={
                                                            zpAR_Svelte.fetch_query
                                                      }
                                                /></td
                                          >
                                    </tr>
                                    <tr><td>&nbsp;</td></tr>
                                    <tr>
                                          <td
                                                colspan="2"
                                                class="td50 defaultPadding center"
                                          >
                                                <button
                                                      on:click|preventDefault={handle_load}
                                                      >Load</button
                                                >
                                                <button
                                                      on:click|preventDefault={handle_zpEventDetails}
                                                      formaction={zpAR_Svelte.action}
                                                      value={zpAR_Svelte.value}
                                                      name="zp_button"
                                                      >ZP_EventDetails</button
                                                >
                                                <button
                                                      on:click|preventDefault={handle_urlRouteString}
                                                      formaction={zpAR_Svelte.action}
                                                      value={zpAR_Svelte.value}
                                                      >urlRoute</button
                                                >
                                          </td>
                                    </tr>
                              </tbody>
                        </table>
                        </form>
                  </td>
                  <td class="td50 top">
                        <div
                              style="max-height:15em;width:100%; overflow:auto; padding:0"
                        >
                              <VarDump
                                    title="vardump"
                                    {vardump}
                                    dumpAll={false}
                              />
                        </div>
                  </td>
            </tr>
            <tr>
                  <td class="">
                        <VarDump
                              title="ZP_ApiRouter (Svelte)"
                              vardump={zpAR_Svelte}
                        />
                  </td>
                  <td>
                        <VarDump
                              title="ZP_ApiRouter (PHP)"
                              vardump={zpAR_zpPHP}
                              dumpAll={true}
                        />
                  </td>
            </tr>
      </tbody>
</table>

<!--



      import { page } from "$app/state";
      import { onMount } from "svelte";
      import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
      import { ZP_EventDetails } from "$lib/zeeltephp/class.zp.eventdetails";
      import { zp_fetch_api, zp_get_eventDetails } from "$lib/zeeltephp/zeeltephp.api";
      import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
      import VarDump from "$lib/VarDump.svelte";
      import { get_typeof, is_typeof } from "$lib/zeeltephp/dev.types";

      export let data_query_results;


      let zpAR_Svelte = new ZP_ApiRouter();
      let zpAR_zpPHP  = null;
      let vardump     = null;
      
      onMount(async () => {
            //default load like +page.js
            //zpAR_zpPHP = await zp_fetch_api(fetch, zpAR_Svelte);
            //zpAR_zpPHP = zpAR_zpPHP?.data || {};
      });

      function zp_fetch() {

      }


      async function handle_click(event) {
            console.clear();
            vardump = {
                  name:   'handle_click()',
                  typeof:  get_typeof(event),
                  instanceof: is_typeof('object', event)
            }
            //vardump = get_type_of(new ZP_EventDetails());
            //vardump = new ZP_EventDetails(event)
            //zpAR_Svelte = new ZP_ApiRouter(page);
            //vardump = event;
            //vardump = page;
            //vardump = new ZP_EventDetails(page.url.searchParams);
            //zpAR_Svelte = new ZP_ApiRouter(e);
            //
            //zpAR_zpPHP = await zp_fetch_api(fetch, zpAR_Svelte);
            //zpAR_zpPHP = zpAR_zpPHP?.data || {};
            //
      }

      async function handle_zpEventDetails() {
            console.clear();
            vardump = 'handle_zpEventDetails'
      }

      async function handle_urlRouteString() {
            console.clear();
            vardump = undefined
            vardump = 'handle_urlRouteString'
            //zpAR_zpPHP  = {};
            //zpAR_Svelte = new ZP_ApiRouter(urlRouteString);
            //data = urlRouteString;      
      }


</script>

<table class="tableCompact">
      <!--
      <thead>
            <tr>
                  <td class="td50 zpFlexRow">
                        <span class="nowrap">Test your Query</span>
                        <span class="zpFlexRow" style="position:abolute; right:0px;">
                              <button on:click={handle_click} formaction="{zpAR_Svelte.action}" value="{zpAR_Svelte.value}">Click</button>
                              <button on:click={handle_urlRouteString} formaction="{zpAR_Svelte.action}" value="{zpAR_Svelte.value}">urlRoute</button>
                        </span>
                  </td>
                  <td class="td50" rowspan="2">
                        <VarDump title="VarDump / Query Results" vardump={vardump} />
                  </td>
            </tr>
      </thead>
      -- >
      <tbody>
            <tr>
                  <td class="td50  tableTHEAD">
                        <div style="position:relative">
                              Test your Query

                              <div class="zpFlexRow" style="position:absolute; top: 0; right:0;">
                                    <button on:click={handle_click} formaction="{zpAR_Svelte.action}" value="{zpAR_Svelte.value}">
                                          ButtonEvent
                                    </button>
                                    &nbsp;
                                    <button on:click={handle_zpEventDetails} formaction="{zpAR_Svelte.action}" value="{zpAR_Svelte.value}">
                                          ZP_EventDetails
                                    </button>
                                    &nbsp;
                                    <button on:click={handle_urlRouteString} formaction="{zpAR_Svelte.action}" value="{zpAR_Svelte.value}">
                                          urlRoute
                                    </button>
                                    &nbsp;

                              </div>
                        </div>
                  </td>
                  <td class="td50" rowspan="2">
                        <VarDump title="VarDump / Query Results" vardump={vardump} />
                  </td>
            </tr>
            <tr>
                  <td>
                        <table>
                              <tbody>
                                    <tr>
                                          <td width="1">ZeeltePHP_Base</td>
                                          <td>{PUBLIC_ZEELTEPHP_BASE}</td>
                                    </tr>
                                    <tr>
                                          <td width="1">zp_route</td>
                                          <td>{page.url.pathname}</td>
                                    </tr>
                              <tr>
                                    <td>zp_action</td>
                                    <td class="zpFlexRow">
                                          <input type="text" name="action" bind:value={zpAR_Svelte.action}>
                                          <input type="text" name="value"  bind:value={zpAR_Svelte.value}>
                                    </td>
                              </tr>
                              <tr>
                                    <td>zp_data/params</td>
                                    <td><input type="text" name="params" bind:value={zpAR_Svelte.fetch_query}></td>
                              </tr>
                              <tr>
                                    <td>urlRoute</td>
                                    <td><input type="text" name="urlRouteString" bind:value="{zpAR_Svelte.fetch_query}"></td>
                              </tr>
                              </tbody>
                        </table>
                  </td>
            </tr>
      </tbody>
</table>


<table class="tableCompact">
      <tbody>
            <tr>
                  <td class="td50"><VarDump title="ZP_ApiRouter (Svelte)" vardump={zpAR_Svelte} /></td>
                  <td class="td50">
                        <VarDump title="ZP_ApiRouter (PHP)"    vardump={zpAR_zpPHP} />
                  </td>
            </tr>
      </tbody>
</table>



-->
