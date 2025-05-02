<script>
    import { page } from '$app/state';
    import { onMount } from 'svelte';
    import { zp_fetch_api } from 'zeeltephp';

    onMount(() => {
        zp_fetch_api(fetch, page.url);
    })
</script>

<h1>Welcome to ZeeltePHP-SvelteKit</h1>
<p>Visit <a href="https://github.com/derharry/svelte-zeeltephp">https://github.com/derharry/svelte-zeeltephp</a> to read the documentation</p>
<p>Visit <a href="https://svelte.dev/docs/kit">svelte.dev/docs/kit</a> to read the documentation</p>
