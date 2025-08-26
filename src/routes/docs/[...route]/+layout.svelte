<script>

     let {
          data,
          children
     } = $props()

     console.log(data)

</script>

<main>
     <nav>
          {#each data.mainToc as toc}
               
               <div>
                    <span>{toc.label}</span>
                    
                    {#each toc.articles as article}
                         <br><a
                              href="/docs/{toc.route}#{article.anchor}"
                         >{article.label}</a>
                    {:else}
                    -
                    {/each}
               </div>

          {:else}
               -no toc data-
          {/each}
     </nav>

     <article>
          {@render children()}
     </article>
</main>

<style>

     main {
          display: grid;
          grid-template-columns: auto 1fr;
     }

     nav {
          width: fit-content;
          min-width: fit-content;
          background-color: aqua;
          padding: 0.5em;
     }

     nav div {
          padding-bottom: 1.25em;
     }

     span {
          font-size: 1.25em;
          font-weight: 800;
     }
     
     article {
          background-color: blue;
     }

     a {
          text-decoration: none;
     }
     a:hover {
          text-decoration: underline;
     }

</style>