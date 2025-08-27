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

<div class="nav" {...attrsNAV}>

     <div class="slot-logo">
     <slot name="logo">Default Logo</slot>
     </div>

     <div class="slot-middle">
     <slot name="middle">
          {#each data_nav as navObj}
               <a 
                    {...attrsA}
                    href={navObj.url}
                    onclick={()=> route = navObj.url}
                    class:active={route == navObj.url}
               >{@html navObj.label}</a>
          {/each}
     </slot>
     </div>

     <div class="slot-right">
     <slot name="right">
     </slot>
     </div>

</div>

<style>

     .nav {
          display: grid;
          grid-template-columns: auto 1fr auto;
          padding: 0.5em;
          height: fit-content;
          vertical-align: middle;
          align-items: center;
     }

     .slot-logo {
          /*/background: red;/**/
     }

     .slot-middle {
          /*/background: blue;/**/
          display: flex;
          justify-content: center;
          gap: 1em;
          flex-direction: row;
          align-items: middle;
          text-align: center;
     }

     .slot-right {
          /*/background: yellow;/**/
          text-align: right;
     }

     .active {
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