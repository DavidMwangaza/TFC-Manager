import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import * as marked from 'marked';
import puppeteer from 'puppeteer';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

function escapeHtml(html) {
  if (html === null || html === undefined) return '';
  const s = String(html);
  return s
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

async function main() {
  const root = path.resolve(__dirname, '..');
  const tfcPath = path.join(root, 'TFC-DOCUMENT.md');
  const figPath = path.join(root, 'DIAGRAMMES-PAR-FIGURE-NB.md');
  const chapPath = path.join(root, 'DIAGRAMMES-PAR-CHAPITRE-NB.md');

  if (!fs.existsSync(tfcPath)) {
    console.error('Fichier TFC-DOCUMENT.md introuvable dans le dépôt.');
    process.exit(1);
  }

  let tfc = fs.readFileSync(tfcPath, 'utf8');
  // Replace LaTeX-style page breaks with HTML page-breaks
  tfc = tfc.replace(/\\newpage/g, '<div style="page-break-before:always"></div>');

  let annex = '';
  if (fs.existsSync(figPath)) {
    annex += '\n\n# ANNEXES — DIAGRAMMES (par figure)\n\n';
    annex += fs.readFileSync(figPath, 'utf8');
  }
  if (fs.existsSync(chapPath)) {
    annex += '\n\n# ANNEXES — DIAGRAMMES (par chapitre)\n\n';
    annex += fs.readFileSync(chapPath, 'utf8');
  }

  const fullMd = tfc + '\n\n' + annex;

  const renderer = new marked.Renderer();
  renderer.code = function(code, infostring, escaped) {
    const lang = (infostring || '').trim().split(/\s+/)[0];
    if (lang === 'mermaid') {
      return `<div class="mermaid">${escapeHtml(code)}</div>`;
    }
    return `<pre><code>${escapeHtml(code)}</code></pre>`;
  };

  const htmlContent = marked.parse(fullMd, { renderer });

  const html = `<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>TFC - PDF</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.1.0/github-markdown-light.min.css">
<style>
  body { box-sizing: border-box; padding: 28px; }
  .markdown-body { max-width: 900px; margin: auto; }
  .mermaid { text-align: center; margin: 16px 0; }
  img { max-width: 100%; height: auto; }
</style>
</head>
<body>
<article class="markdown-body">
${htmlContent}
</article>
<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
<script>
  mermaid.initialize({ startOnLoad: false, theme: 'base', securityLevel: 'loose' });
  document.addEventListener('DOMContentLoaded', function() {
    try {
      mermaid.init(undefined, document.querySelectorAll('.mermaid'));
    } catch (e) {
      console.error('mermaid render error', e);
    }
  });
</script>
</body>
</html>`;

  const outDir = path.join(root, 'build');
  await fs.promises.mkdir(outDir, { recursive: true });
  const htmlPath = path.join(outDir, 'tfc_render.html');
  await fs.promises.writeFile(htmlPath, html, 'utf8');

  console.log('Fichier HTML généré:', htmlPath);

  const browser = await puppeteer.launch({ args: ['--no-sandbox', '--disable-setuid-sandbox'] });
  const page = await browser.newPage();
  await page.goto('file://' + htmlPath, { waitUntil: 'networkidle0' });

  // Force mermaid init in the page and wait for SVGs to appear (timeout after 5s)
  try {
    const rendered = await page.evaluate(async () => {
      try {
        if (typeof mermaid !== 'undefined') {
          try { mermaid.initialize({ startOnLoad: false, theme: 'base', securityLevel: 'loose' }); } catch (e) { /* ignore */ }
        }

        const mermaidNodes = Array.from(document.querySelectorAll('.mermaid'));
        mermaidNodes.forEach(node => {
          try { mermaid.init(undefined, node); } catch (e) { console.error('mermaid.init error', e); }
        });

        const waitForSvgs = () => new Promise(resolve => {
          const start = Date.now();
          const interval = setInterval(() => {
            const nodes = Array.from(document.querySelectorAll('.mermaid'));
            const allHave = nodes.length === 0 ? true : nodes.every(n => n.querySelector('svg'));
            if (allHave || (Date.now() - start) > 5000) {
              clearInterval(interval);
              resolve(allHave);
            }
          }, 200);
        });

        return await waitForSvgs();
      } catch (e) {
        console.error('Error during mermaid rendering check', e);
        return false;
      }
    });

    console.log('Mermaid rendu (présence SVGs) :', rendered);
  } catch (e) {
    console.warn('Échec de la vérification de rendu Mermaid :', e);
  }

  const pdfPath = path.join(outDir, 'TFC.pdf');
  await page.pdf({ path: pdfPath, format: 'A4', printBackground: true });
  await browser.close();

  console.log('PDF généré:', pdfPath);
}

main().catch(err => {
  console.error(err);
  process.exit(1);
});
