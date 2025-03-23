<script>
      import { page } from "$app/state";
      import VarDump from "$lib/VarDump.svelte";
      import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";
      import { onMount } from "svelte";
      

      let promise_load
      let data_load

      let sqlStatement = "SELECT 'Hello World from default query'";


      onMount(() => {
            promise_load = zp_fetch_api(fetch)
                  .then((data) => data_load = data)
                  .catch((error) => data_load = error)
      })



      function handle_dbwp (e) {
            try {
                  data_load    = null
                  promise_load = zp_fetch_api(fetch, e)
                        .then((data)   => data_load = data)
                        .catch((error) => data_load = error)
            } catch (error) {
                  data_load = error
                  console.log(error);
            }
      }

</script>

<h1>Database ConnectionURL</h1>

<button
      on:click={handle_dbwp}
      formaction="?/test_db_query"
      name="sqlStatement"
      value={sqlStatement}
>
      {#await promise_load}
            requesting db..
      {:then _}
            Test WP-DB
      {:catch error}
            upsie WP-DB <br>{error.message} {error.error}
      {/await}
</button>

<textarea bind:value={sqlStatement}></textarea>

<VarDump title="response" vardump={data_load}/>