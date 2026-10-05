
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <title>VibeGuard — Universal Code Auditor</title>

  <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<div class="shell">

<!-- HEADER -->
<header>

    <div class="brand-image">

        <img
            src="assets/Logo_.png"
            alt="Zeba Academy"
            class="brand-logo"
        >

    </div>

</header>


  <!-- MAIN CONTENT -->
  <main>

    <section class="hero">
      <h1>
        Universal Code<br>
        <span>Forensics.</span>
      </h1>

      <p class="sub">
        Upload a source-code file in a supported programming language,
        or paste code. VibeGuard analyses source text locally with
        deterministic structural heuristics—submitted code is never executed.
      </p>

    </section>


    <!-- ANALYZER CARD -->
    <section class="card">

      <div class="tabs">

        <button
          class="tab active"
          data-tab="upload">
          UPLOAD FILE
        </button>

        <button
          class="tab"
          data-tab="paste">
          PASTE CODE
        </button>

      </div>


      <!-- UPLOAD PANEL -->
      <div
        id="uploadPanel"
        class="panel active">

        <label
          class="drop"
          id="drop">

          <input
            type="file"
            id="file">

          <strong>
            Drop a source-code file here
          </strong>

          <span>
            or click to browse · common programming-language
            extensions supported
          </span>

        </label>

      </div>


      <!-- PASTE PANEL -->
      <div
        id="pastePanel"
        class="panel">

        <textarea
          id="code"
          placeholder="Paste source code here…">
        </textarea>

        <div class="row">

          <input
            id="filename"
            value="pasted.txt"
            aria-label="filename">

          <button
            id="analyzePaste"
            class="primary">
            ANALYZE CODE
          </button>

        </div>

      </div>


      <!-- OUTCOMES -->
      <div class="outcomes">

        <span>
          <b>0</b> CLEAN
        </span>

        <span>
          <b>1</b> SUSPICIOUS
        </span>

        <span>
          <b>2</b> FLAGGED
        </span>

        <span>
          <b>3</b> FATAL
        </span>

      </div>


      <div class="hint">
        Supports PHP, Python, JavaScript/TypeScript, Java, C/C++,
        C#, Go, Rust, Ruby, Swift, Kotlin, Dart, Scala, R, Lua,
        Perl, Shell, SQL, HTML/CSS, Vue, Svelte, and many other
        text-based source files. No code is executed.
      </div>

    </section>


    <!-- RESULTS -->
    <section
      id="result"
      class="result hidden">
    </section>

  </main>


  <!-- FOOTER -->
 <!-- FOOTER -->
<footer class="site-footer">

  <div class="footer-content">

    <div class="footer-left">
      <img
        src="assets/Logo_.png"
        alt="Zeba Academy"
        class="footer-logo"
      >

      <div>
        <strong>VibeGuard</strong>
        <span>Universal Code Auditor</span>
      </div>
    </div>



    <div class="footer-right">
      <span>Exit codes:</span>
      <b>0</b> Clean
      <b>1</b> Suspicious
      <b>2</b> Flagged
      <b>3</b> Fatal
    </div>

  </div>

</footer>

</div>


<script src="assets/app.js"></script>

</body>
</html>
