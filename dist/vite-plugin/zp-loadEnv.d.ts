/**
 * Load projects .env files.
 * If .env-file is not found, values are generated from source path /your/path/htdocs/<your-svelte-project>
 * @param {*} mode  from  @/vite.config.js  defineConfig(({ mode }) return { zeeltephp_loadEnv(mode); }
 * @returns
 */
export function zeeltephp_loadEnv(mode: any): void;
