<script>

      import VarDump from "$lib/VarDump.svelte";
      
      export let data;


</script>


<h1>Welkom to ZeeltePHP</h1>
<VarDump title="page.data" vardump={data}/>
<VarDump />