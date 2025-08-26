<script>
     import { validAnchorID } from "zeelte";
     
     let {
          toc_data = [],
          children
     } = $props();

</script>

<main>
     <nav>
          {#each toc_data as topic}
               <div>
                    <span>{topic.label}</span>
                    {#each topic.articles as article}
                         <a href="/docs/{article.href}/{article?.anchor ? '#'+validAnchorID(article.anchor) : ''}" class="a-topic">{article.label}</a>
                         {#each article.tocs as toc}
                             <a href="/docs/{article.href}/{toc?.anchor ? '#'+validAnchorID(toc.anchor) : ''}" class="a-toc">{toc.label}</a>
                         {/each}
                    {/each}
               </div>
          {:else}
               no data
          {/each}
     </nav>

      <section>
          {@render children()}
      </section>
</main>

<style>

     main {
          display: grid;
          grid-template-columns: auto 1fr;
          gap: 0.5rem;
     }

     nav {
          background-color: none;
     }

     div {
          display: grid;
          grid-template-columns: 1;
          margin:  0;
          padding: 0;
          padding-bottom: 1rem;
          background-color: none;
          padding-bottom: 2rem;
     }

     span {
          margin:  0;
          padding: 0;
          font-size: 1.25rem;
          font-weight: bold;
     }

     a {
          background-color: none;
     }

     .a-topic {
          font-size: 1.1rem;
     }

     .a-toc {
          padding-left: 0.25rem;
          font-size: 1rem;
     }

     section {
          background-color: yellowgreen;
          overflow: auto;
          max-height: 30em;
     }

</style>