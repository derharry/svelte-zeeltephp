import fs from 'fs/promises';
import path from 'path';

export async function load({ params }) {
  const topic = params.topic; // e.g. '1_example'
  const docsDir = path.resolve('src/routes/docs', topic);

  // Find first .md and .toc.json in the folder (or use a naming pattern)
  const files = await fs.readdir(docsDir);
  const mdFile = files.find(f => /\.md$/i.test(f));
  const tocFile = files.find(f => /\.toc\.json$/i.test(f));

  let markup = '';
  let tocs   = [];

  if (mdFile) {
    markup = await fs.readFile(path.join(docsDir, mdFile), 'utf-8');
  }
  if (tocFile) {
    const tocRaw = await fs.readFile(path.join(docsDir, tocFile), 'utf-8');
    try { tocs = JSON.parse(tocRaw); } catch {}
  }

  return { markup, tocs, topic };
}
