<script>

      import VarDump from '$lib/VarDump.svelte';

      export let data;
      $: console.log(data);

</script>

<VarDump title="PHP responses from routes" vardump={data} />
