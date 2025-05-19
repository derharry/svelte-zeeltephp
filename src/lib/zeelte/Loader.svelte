<!--
     Loader.svelte
     A simple HTML marquee animation

     Usage
          <Loader value="text to animate" />
          <Loader> any content to animate </Loader>

     v2025.05.01

-->
<script>
     /** {string} Value to anmiate */
     export let value = "?";
     /** {string} Value to anmiate */
     export let padding = "0px";
     /** {string} Animation direction Left|Right|Up|Down */
     export let direction = "down";
</script>

<!-- svelte-ignore a11y_distracting_elements -->
<marquee {direction} style="padding:{padding}">
     <slot>
          {@html value}
     </slot>
</marquee>

<style>
     marquee {
          text-align: center;
          display: inline-block;
          width: fit-content;
          height: fit-content;
          background-color: none;
     }
</style>
