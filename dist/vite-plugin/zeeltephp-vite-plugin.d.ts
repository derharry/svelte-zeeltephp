/**
 * ZeeltePHP Vite Plugin
 * ---------------------
 * Integrates ZeeltePHP into Vite/SvelteKit projects by:
 * 1. Loading/generating environment variables
 * 2. Handling post-build operations for PHP integration
 *
 * Usage in vite.config.js:
 * plugins: [zeeltephp(mode)]
 *
 * Version 2 - 2025.05.24
 *
 * @param {string} mode - Vite mode (development/production)
 * @returns {import('vite').Plugin} Vite plugin object
 */
export function zeeltephp(mode: string): import("vite").Plugin;
