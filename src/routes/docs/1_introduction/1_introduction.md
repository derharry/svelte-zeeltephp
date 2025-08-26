# ZeeltePHP (aka SveltePHP)

## What is ZeeltePHP
ZeeltePHP is designed as add-on for SvelteKit projects with Adapter-Static and enables PHP backend 
integration using SvelteKit-style +server file conventions, e.g. +page.server.php, +layout.server.php, +server.php. It mimics the behaviour of SvelteKit.

To switch between SvelteKit-native or using ZeeltePHP, ZeeltePHP provides 
methods to minimize the lines of code to change to a minimum. 


## Before we begin
ZeeltePHP requires you to have basic know-how of Svelte, SvelteKit and PHP.


## Who is it for
1. For projects with adapter-static: Frontend JavaScript, Backend PHP
2. For projects in co-existence, SvelteKit + PHP


### Use-Cases
1. SvelteKit    
   - hosting:    NodeJS
   - stack:      SvelteKit-native

2. Adapter-Static + PHP
   - hosting:    Apache, Nginx, ... with PHP
   - stack:      SvelteKit-Adapter-Static, PHP
   - 
3. Mixed (SvelteKit + PHP)
   - hosting:    NodeJS + static-webhosting
   - stack:      SvelteKit, SvelteKit-Adapter-Static, PHP

4. Beeing in a ready-to-port state each way, full SvelteKit or PHP.
5. Learn both environments: NodeJS and static-hosting with PHP


