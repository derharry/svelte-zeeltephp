import { $ } from "bun";

// 1. Build project
//await $`bun run build`;

// 2. Copy assets
await $`cp -r ./static/api/ ./dist/api`;

// 3. Deploy
console.log("🚀 Deploying...");
// Add deployment logic