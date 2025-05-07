<script>
     export let value = "?";
     export let padding = "0px";
     export let direction = "down";
</script>

<!-- svelte-ignore a11y_distracting_elements -->
<marquee {direction} style="padding:{padding}">{@html value}</marquee>

<style>
     marquee {
          width: fit-content;
          height: fit-content;
          background-color: orange;
          color: black;
          border: 2px darkorange solid;
          border-radius: 0.5em;
          font-style: italic bold;
          text-align: center;
     }
</style>
