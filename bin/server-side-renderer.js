try {
  const { createBundleRenderer } = require("vue-server-renderer");

  process.stdin.setEncoding("utf8");
  let domInline = "";

  process.stdin.on("readable", () => {
    //We will see later how Symfony communicates on this input
    let chunk;

    // Use a loop to make sure we read all available data.
    while ((chunk = process.stdin.read()) !== null) {
      domInline += chunk;
    }
  });

  process.stdin.on("end", () => {
    try {
      const fullDom = domInline.toString();
      const bundle = require(process.cwd() +
        "/build/vue-ssr-server-bundle.json");
      const renderer = createBundleRenderer(bundle, {
        template: fullDom
      });

      const stream = renderer.renderToStream({
        user: JSON.parse(process.argv.slice(2)[0])
      });
      stream.pipe(process.stdout);
    } catch (e) {
      process.stdout.write(domInline.toString());
    }
  });
} catch (e) {
  process.stdout.write(domInline.toString());
}
