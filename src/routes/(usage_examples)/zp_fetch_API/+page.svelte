<script lang="ts">

     import { onMount } from "svelte";
     import { zp_fetch, zp_fetch_api, HTML_Marquee } from "zeeltephp"

     export let data

     let promise

     onMount(() => {
          //data = zp_fetch(fetch, 'api/apidemo', 'GET', null, true)
          promise = zp_fetch('/zpdev', { method: 'GET' })
     })

</script>

<div class="grid-container">

     
     <button 
          on:click={() => {   
               //data = zp_fetch(fetch, 'api/apidemo', 'GET', null, true)   
               promise = zp_fetch('/zpdev', { method: 'GET' })   
          }}>GET</button>
     <span>
          Execute GET
     </span>



     <button on:click={() => {   promise = zp_fetch('/zpdev', { method: 'POST' })   }}>POST</button>
     <span>
          Execute POST
     </span>



     <button on:click={() => {   promise = zp_fetch('/zpdev', { method: 'PUT' })   }}>PUT</button>
     <span>
          Execute PUT
     </span>



     <button on:click={() => {   promise = zp_fetch('/zpdev', { method: 'PATCH' })   }}>PATCH</button>
     <span>
          Execute PATCH
     </span>



     <button on:click={() => {   promise = zp_fetch('/zpdev', { method: 'DELETE' })   }}>DELETE</button>
     <span>
          Execute DELETE
     </span>



     <button 
          on:click={() => {   
               promise = zp_fetch('/zpdev', { method: 'HEAD', fetch: fetch })   
          }}

          >HEAD</button>
     <span>
          Execute HEAD
     </span>



     <button on:click={() => {   promise = zp_fetch('/zpdex', { method: 'POST' })   }}>none</button>
     <span>
          Execute a non existing route
     </span>


     {#await promise}
          <pre><HTML_Marquee value="🐘" /></pre>
     {:then data}
          <pre>{JSON.stringify(data, null, 2)}</pre>
     {/await}
     
</div>


<style>
     .grid-container {
          display: grid;
          grid-template-columns: 125px 1fr 1fr; /* button span pre */
          grid-auto-rows: min-content;
          padding:        0 1em;
          gap:            0.5em;   /* rows and cols */
          column-gap:     1em;     /* cols */
          width:          100%;
          align-items:    center;
     }

     button:nth-of-type(-n+7) {
          grid-column:   1;
          width:         100%;
     }

     span:nth-of-type(-n+7) {
          grid-column: 2;
          align-self:  center;
     }

     /* Optional styling */
     button {
          width: 100%;
     }
     span {
          align-self: center;
     }

     pre {
          grid-column: 3;
          grid-row:    1 / span 7; /* rowspan 7 */
          box-sizing:  border-box;
          align-self:  stretch;
     }
</style>

