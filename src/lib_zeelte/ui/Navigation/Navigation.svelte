<script>

     let {
          attrsNAV = '',
          attrsUL  = '',
          attrsLI  = '',
          attrsA   = '',
          data_nav = [ 
               {  
                    label: 'Example 1', 
                    url:   './link_1',
               },
               {  
                    label: 'Example 2', 
                    url:   './link_2',
               } 
          ]
     } = $props()

     let route = $state('/')

</script>

<nav {...attrsNAV}>
     <ul {...attrsUL}>

          {#each data_nav as navObj}

               <li {...attrsLI}>
                    <a 
                         {...attrsA}
                         href={navObj.url}
                         onclick={()=> route = navObj.url}
                         class:active={route == navObj.url}
                    >{@html navObj.label}</a>
                    <!--<br>{route}-->
               </li>

          {/each}

     </ul>
</nav>

<style>

     .active {
          background: red;
     }

</style>


<!--
<script>
     import { readable } from 'svelte/store';
     import { page }     from '$app/state';

     let {
          attrsNAV = '',
          attrsUL  = '',
          attrsLI  = '',
          attrsA   = '',
          data_nav = [ 
               {  
                    label: 'Example 1', 
                    url:   './link_1',
               },
               {  
                    label: 'Example 2', 
                    url:   './link_2',
               } 
          ]
     } = $props()

     let route = $state('/')

</script>

<nav {...attrsNAV}>
     <ul {...attrsUL}>

          {#each data_nav as navObj}

               <li {...attrsLI}>
                    <a 
                         {...attrsA}
                         href={navObj.url}
                         onclick={()=> route = navObj.url}
                         class:active={route == navObj.url}
                    >{@html navObj.label}</a>
                    <!--<br>{route}-- >
               </li>

          {/each}

     </ul>
</nav>

<style>

     .active {
          background: red;
     }

</style>
-->