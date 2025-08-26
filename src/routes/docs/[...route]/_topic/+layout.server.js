import fs from 'fs/promises';
import path from 'path';

export async function load() {
  const docsDir = path.resolve('src/routes/docs');
  const entries = await fs.readdir(docsDir, { withFileTypes: true });
  const topics = entries.filter(e => e.isDirectory() && /^\d+_.*/.test(e.name));

  let tocData = [];

  for (const topic of topics) {
    const topicPath = path.join(docsDir, topic.name);
    const files = await fs.readdir(topicPath);
    
    // Find markdown files
    const mdFiles = files.filter(f => /\.md$/i.test(f));

    for (const mdFile of mdFiles) {
      // Build toc.json filename based on markdown filename
      const baseName = path.basename(mdFile, path.extname(mdFile));
      const tocFileName = `${baseName}.toc.json`;
      const tocFilePath = path.join(topicPath, tocFileName);

      let topicToc = [];
      try {
        const tocRaw = await fs.readFile(tocFilePath, 'utf-8');
        topicToc = JSON.parse(tocRaw);
      } catch (error) {
        // No TOC file or parsing error, continue with empty TOC
        topicToc = [];
      }

      tocData.push({
        label: topic.name.replace(/^\d+_/, ''),
        route: topic.name,
        basename: baseName,
        articles: topicToc
      });
    }
  }

  return {
    toc_data: tocData
  };
}
