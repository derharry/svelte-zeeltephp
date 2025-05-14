<script>
     export let value = "?";
     export let padding = "0px";
     export let direction = "down";
</script>

<!-- svelte-ignore a11y_distracting_elements -->
<marquee {direction} style="padding:{padding}">{@html value}</marquee>

<style>
     marquee {
          font-style: italic bold;
          text-align: center;
          display: inline-block;
          width: fit-content;
          height: fit-content;
          b_ackground-color: orange;
          c_olor: black;
          bo_rder: 2px darkorange solid;
          bo_rder-radius: 0.5em;
     }
</style>
