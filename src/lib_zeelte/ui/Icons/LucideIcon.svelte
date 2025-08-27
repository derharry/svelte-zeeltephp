<!--

<LucideIcon icon="" />
<LucideIcon icon="" cssClass="" cssStyle="">

Load SVG-icons from CDN for Lucide-static-Icons by given icon-name

See available icons:
    Visit Lucide-Icons https://lucide.dev/icons/
    Use <LucideIconSearch />
Default <LucideIcon icon="circle-help" cssClass="" cssStyle="font-size:1em">  

v1, v2025-05-14, Svelte 4, 5 - init
v2, v2025-07-01, Svelte 4, 5 - fix: style @font-face to unpkg did not work anymore
v3, v2025-08-27, Svelte 5 - CDN and unpkg has changed. v3 based on: https://lucide.dev/guide/packages/lucide

-->
<script lang="ts">
    import { browser } from '$app/environment'
    import { onMount } from 'svelte';

    let {
        name     = 'circle-question-mark',
        cssClass = '',
        cssStyle = 'font-size:1em',
    } = $props()

    if (browser && window && window.lucide) {
        window.lucide.createIcons();
    }

    onMount(() => {
        if (window.lucide) {
            window.lucide.createIcons();
        } else {
            console.warn('Lucide script not loaded yet');
        }
    });

</script>

<!-- load CDN into head (1-time) -->
<svelte:head>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
</svelte:head>

<i data-lucide={name} {cssClass} {cssStyle}></i>