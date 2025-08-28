<script>
     import { validAnchorID } from "zeelte";
     
     let {
          toc_data = [],
          children
     } = $props();

</script>

<main>
     <div class="toc">
          {#each toc_data as topic}
               <div>
                    <span>{topic.label}</span>
                    {#each topic.articles as article}
                         <a href="/docs/{topic.route}{article.anchor ? '#'+validAnchorID(article.anchor) : ''}" class="a-topic">{article.label}</a>
                         {#each article.tocs as toc}
                             <a href="/docs/{topic.route}{toc?.anchor ? '#'+validAnchorID(toc.anchor) : ''}" class="a-toc">{toc.label}</a>
                         {/each}
                    {/each}
               </div>
          {:else}
               -no toc data-
          {/each}
     </div>

     <article>
          {@render children()}
     </article>
</main>

<style>
     main {
          display: grid;
          grid-template-columns: auto 1fr;
          /**grid-template-rows: 1fr;*/
          gap: 0;
          height: 100%;
          min-height: 0
     }

     .toc {
          width: fit-content;
          overflow: auto;
          max-height: 100%;
          padding: 0.5em;
          min-height: 0;
     }

     .toc > div {
          background-color: none;
          display: grid;
          grid-template-columns: 1;
          margin:  0;
          padding: 0;
          margin-bottom: 2rem;
     }

     span {
          margin:  0;
          padding: 0;
          font-size: 1.25rem;
          font-weight: bold;
     }

     .a-topic {
          font-size: 1.1rem;
     }

     .a-toc {
          padding-left: 0.25rem;
          font-size: 1rem;
     }

     article {
          background-color: none;
          overflow: auto;
          max-height: 100%;
          min-height: 0;
     }

     a {
          text-decoration: none;
     }
     a:hover {
          text-decoration: underline;
     }

</style>