<script>
     import { Marked } from "zeelte/ui";

     let {
          data
     } = $props()

</script>


<div>
     <Marked markdown={data.markdown}/>

     {#if !data?.markdown} 

          Documentation

          Head to <a href="/docs/1_example">1_example</a>

     {/if}

</div>
