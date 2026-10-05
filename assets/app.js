const $ = (s) => document.querySelector(s),
  result = $("#result");
document.querySelectorAll(".tab").forEach(
  (t) =>
    (t.onclick = () => {
      document
        .querySelectorAll(".tab")
        .forEach((x) => x.classList.remove("active"));
      t.classList.add("active");
      document
        .querySelectorAll(".panel")
        .forEach((x) => x.classList.remove("active"));
      $(
        "#" + (t.dataset.tab === "upload" ? "uploadPanel" : "pastePanel"),
      ).classList.add("active");
    }),
);
$("#file").onchange = (e) => {
  if (e.target.files[0]) analyzeFile(e.target.files[0]);
};
const drop = $("#drop");
["dragenter", "dragover"].forEach((x) =>
  drop.addEventListener(x, (e) => {
    e.preventDefault();
    drop.classList.add("over");
  }),
);
["dragleave", "drop"].forEach((x) =>
  drop.addEventListener(x, (e) => {
    e.preventDefault();
    drop.classList.remove("over");
  }),
);
drop.addEventListener("drop", (e) => {
  if (e.dataTransfer.files[0]) analyzeFile(e.dataTransfer.files[0]);
});
$("#analyzePaste").onclick = () =>
  analyzeCode($("#code").value, $("#filename").value || "pasted.txt");
async function analyzeFile(file) {
  const fd = new FormData();
  fd.append("file", file);
  showLoading(file.name);
  await request(fd);
}
async function analyzeCode(code, name) {
  const fd = new FormData();
  fd.append("code", code);
  fd.append("filename", name);
  showLoading(name);
  await request(fd);
}
async function request(fd) {
  try {
    const r = await fetch("api/analyze.php", { method: "POST", body: fd });
    const text = await r.text();
    let d;
    try {
      d = JSON.parse(text);
    } catch (e) {
      d = {
        ok: false,
        exit_code: 3,
        classification: "FATAL ERROR",
        error: text || "Analyzer returned HTTP " + r.status,
      };
    }
    render(d);
  } catch (e) {
    render({
      ok: false,
      exit_code: 3,
      classification: "FATAL ERROR",
      error: "Browser could not reach the analyzer.",
    });
  }
}
function showLoading(name) {
  result.classList.remove("hidden");
  result.innerHTML = `<div class="loading">Analyzing <b>${esc(name)}</b>…</div>`;
  result.scrollIntoView({ behavior: "smooth", block: "center" });
}
function render(d) {
  if (!d.ok) {
    result.innerHTML = `<div class="fatal"><div class="big">3</div><div><div class="label">FATAL ERROR</div><h2>${esc(d.error || "Analysis failed")}</h2><p>The source was not executed. Check the file and try again.</p></div></div>`;
    return;
  }
  const cls = d.exit_code === 0 ? "clean" : d.exit_code === 1 ? "warn" : "flag";
  const signals =
    (d.signals || []).map((x) => `<li>${esc(x)}</li>`).join("") ||
    "<li>No significant heuristic signals detected.</li>";
  result.innerHTML = `<div class="resultHead"><div><span class="label">ANALYSIS RESULT</span><h2>${esc(d.classification)}</h2><p>${esc(d.interpretation)}</p></div><div class="score ${cls}"><strong>${d.score}</strong><span>/ 100</span></div></div>
<div class="grid"><div><small>LANGUAGE</small><b>${esc(d.language)}</b></div><div><small>EXIT CODE</small><b>${d.exit_code}</b></div><div><small>FILE</small><b>${esc(d.file)}</b></div><div><small>LINES</small><b>${d.lines}</b></div><div><small>TOKENS</small><b>${d.tokens}</b></div><div><small>ENTROPY</small><b>${d.entropy} bits</b></div></div>
<div class="signals"><h3>Detected signals</h3><ul>${signals}</ul></div>
<div class="metrics"><span>Tautological comments <b>${d.metrics.tautological_comments}</b></span><span>Defensive guards <b>${d.metrics.defensive_guards}</b></span><span>Fallbacks <b>${d.metrics.fallbacks}</b></span><span>Generic identifiers <b>${d.metrics.generic_identifiers}</b></span></div>`;
  result.scrollIntoView({ behavior: "smooth", block: "center" });
}
function esc(s) {
  return String(s).replace(
    /[&<>"']/g,
    (c) =>
      ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[
        c
      ],
  );
}
