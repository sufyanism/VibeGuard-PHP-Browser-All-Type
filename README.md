# VibeGuard — Universal Code Auditor

A zero-API PHP web interface for deterministic heuristic source-code auditing across many programming languages.

## What changed

The original PHP-only upload restriction has been removed. The UI now accepts source files by extension and sends them to one universal analyzer. The analyzer **never executes uploaded code**.

Supported extension families include PHP, Python, JavaScript/TypeScript, Java, C/C++, C#, Go, Rust, Ruby, Swift, Kotlin, Dart, Scala, R, Lua, Perl, Shell, PowerShell, SQL, HTML/CSS, Vue, Svelte, Solidity, Assembly, Objective-C, Groovy, Elixir, Haskell, Clojure, Julia, Zig, Nim, Fortran, Pascal and Verilog/SystemVerilog. Unknown text-based source extensions are also accepted as `Generic Source`.

## Requirements

- PHP 8.1+
- No Composer packages required.

## Run locally

From this project folder:

```powershell
php -S localhost:8000
```

Open `http://localhost:8000`.

## Upload

Use **UPLOAD FILE** and select or drag in a source file. There is no longer an `accept=".php"` restriction.

## Safety

Submitted source is read as text and analysed; it is not passed to an interpreter, compiler, shell, `eval`, `include`, or `require`. PHP receives real parser validation where PHP is the detected language. Other languages receive conservative structural delimiter validation plus the universal heuristic engine.

For production deployment, configure upload-size limits, HTTPS, authentication if needed, and server-level isolation.

## Exit codes

- 0 — PASS / CLEAN
- 1 — WARNING / SUSPICIOUS
- 2 — FLAGGED / AI BREACH
- 3 — FATAL ERROR

> Heuristic scores are indicators, not proof of authorship or AI generation.

## About Me 
✨ I’m **Sufyan bin Uzayr**, an open-source developer passionate about building and sharing meaningful projects.
You can learn more about me and my work at [sufyanism.com](https://sufyanism.com/) or connect with me on [Linkedin](https://www.linkedin.com/in/sufyanism)

## Your all-in-one learning hub! 
🚀 Explore courses and resources in coding, tech, and development at **zeba.academy** and **code.zeba.academy**. Empower yourself with practical skills through curated tutorials, real-world projects, and hands-on experience. Level up your tech game today! 💻✨

**Zeba Academy**  is a learning platform dedicated to **coding**, **technology**, and **development**.  
➡ Visit our main site: [zeba.academy](https://zeba.academy)   </br>
➡ Explore hands-on courses and resources at: [code.zeba.academy](https://code.zeba.academy)   </br>
➡ Check out our YouTube for more tutorials: [zeba.academy](https://www.youtube.com/@zeba.academy)  </br>
➡ Follow us on Instagram: [zeba.academy](https://www.instagram.com/zeba.academy/)  </br>

**Thank you for visiting!**


