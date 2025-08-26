<script>
  // `data` comes from +page.server.js
  export let data;

  import { marked } from 'marked';

  // Parse markdown to HTML reactively when data.markup changes
  $: htmlMarkup = data.markup
    ? marked.parse(data.markup, { breaks: true, smartypants: true })
    : '';

</script>

<!-- Table of Contents -->
<nav>
  {#if data.tocs?.length}
    <ul>
      {#each data.tocs as toc}
        <li style="margin-left: {(toc.level - 1) * 1}rem;">
          <a href={'#' + toc.anchor}>{toc.label}</a>
        </li>
      {/each}
    </ul>
  {:else}
    <p>No table of contents available.</p>
  {/if}
</nav>

<!-- Rendered Markdown Content -->
<article class="markdown-body">
  {@html htmlMarkup}
</article>

<style>
  nav ul {
    list-style: none;
    padding-left: 0;
  }
  nav li a {
    text-decoration: none;
    color: #0366d6;
  }
  nav li a:hover {
    text-decoration: underline;
  }
  .markdown-body {
    padding: 1rem;
  }
</style>
