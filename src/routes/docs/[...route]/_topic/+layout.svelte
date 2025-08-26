<script>
  export let data; // { toc_data: [ { label, route, articles } ] }
  console.log(data)
</script>

<nav>
  {#if data?.toc_data?.length}
    {#each data.toc_data as topic}
      <section>
        <h2>{topic.label}</h2>
        <ul>
          {#each topic.articles as article}
            <li>
              <a href={`/docs/${topic.route}`}>
                {article.label}
              </a>
              {#if article.articles?.length}
                <ul>
                  {#each article.articles as child}
                    <li>
                      <a href={`${topic.route}#${child.anchor}`}>
                        {child.label}
                      </a>
                    </li>
                  {/each}
                </ul>
              {/if}
            </li>
          {/each}
        </ul>
      </section>
    {/each}
  {:else}
    <p>No TOC data available.</p>
  {/if}
</nav>

<main>
  <slot />
</main>

<style>
  nav {
    max-width: 300px;
    padding: 1rem;
    overflow-y: auto;
    border-right: 1px solid #ddd;
  }
  section {
    margin-bottom: 1.5rem;
  }
  h2 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
  }
  ul {
    list-style: none;
    padding-left: 1rem;
  }
  a {
    color: #0366d6;
    text-decoration: none;
  }
  a:hover {
    text-decoration: underline;
  }
  main {
    padding: 1rem;
  }
</style>
