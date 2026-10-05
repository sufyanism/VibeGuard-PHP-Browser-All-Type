<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function respond(bool $ok, array $data = [], int $http = 200): never {
    http_response_code($http);
    echo json_encode(array_merge(['ok' => $ok], $data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
function fatal(string $message): never {
    respond(false, ['exit_code'=>3,'classification'=>'FATAL ERROR','error'=>$message], 422);
}

/*
 * Universal, non-executing source auditor.
 * The engine intentionally works from source text so one PHP deployment can
 * accept many programming languages without interpreters/compilers installed.
 */
$LANGS = [
 'php'=>['PHP',['php','inc','phtml']],
 'python'=>['Python',['py','pyw']],
 'javascript'=>['JavaScript',['js','mjs','cjs','jsx']],
 'typescript'=>['TypeScript',['ts','tsx']],
 'java'=>['Java',['java']],
 'c'=>['C',['c','h']],
 'cpp'=>['C++',['cpp','cc','cxx','hpp','hh','hxx']],
 'csharp'=>['C#',['cs']],
 'go'=>['Go',['go']],
 'rust'=>['Rust',['rs']],
 'ruby'=>['Ruby',['rb','rake','gemspec']],
 'swift'=>['Swift',['swift']],
 'kotlin'=>['Kotlin',['kt','kts']],
 'dart'=>['Dart',['dart']],
 'scala'=>['Scala',['scala','sc']],
 'r'=>['R',['r','R']],
 'lua'=>['Lua',['lua']],
 'perl'=>['Perl',['pl','pm','pod']],
 'shell'=>['Shell',['sh','bash','zsh','fish']],
 'powershell'=>['PowerShell',['ps1','psm1','psd1']],
 'sql'=>['SQL',['sql']],
 'html'=>['HTML',['html','htm']],
 'css'=>['CSS',['css','scss','sass','less']],
 'vue'=>['Vue',['vue']],
 'svelte'=>['Svelte',['svelte']],
 'solidity'=>['Solidity',['sol']],
 'asm'=>['Assembly',['asm','s']],
 'objectivec'=>['Objective-C',['m','mm']],
 'groovy'=>['Groovy',['groovy']],
 'elixir'=>['Elixir',['ex','exs']],
 'haskell'=>['Haskell',['hs','lhs']],
 'clojure'=>['Clojure',['clj','cljs','cljc','edn']],
 'julia'=>['Julia',['jl']],
 'zig'=>['Zig',['zig']],
 'nim'=>['Nim',['nim']],
 'fortran'=>['Fortran',['f','f90','f95','f03','f08']],
 'pascal'=>['Pascal',['pas','pp']],
 'verilog'=>['Verilog',['v','sv','vh']],
 'solidity'=>['Solidity',['sol']]
];

function languageFor(string $name, array $langs): array {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    foreach ($langs as $id=>$v) if (in_array($ext, $v[1], true)) return [$id,$v[0]];
    return ['text','Generic Source'];
}
function stripStringsAndComments(string $code, string $lang): string {
    // Conservative masking, used only for structural metrics; original code is untouched.
    $s = preg_replace('/\/\*.*?\*\//s', ' ', $code) ?? $code;
    $s = preg_replace('/<!--.*?-->/s', ' ', $s) ?? $s;
    $s = preg_replace('/#(?!\!)[^\r\n]*/', ' ', $s) ?? $s;
    $s = preg_replace('/\/\/[^\r\n]*/', ' ', $s) ?? $s;
    $s = preg_replace('/(["\'])(?:\\\\.|(?!\1).)*\1/s', ' ', $s) ?? $s;
    return $s;
}
function syntaxHint(string $code, string $lang): ?string {
    // PHP gets real parser validation when the host provides the PHP tokenizer.
    if ($lang === 'php') {
        try { token_get_all($code, TOKEN_PARSE); } catch (ParseError $e) {
            return 'PHP syntax parse error' . ($e->getLine() ? ' on line '.$e->getLine() : '') . ': '.$e->getMessage();
        } catch (Throwable $e) { return 'PHP parser failure: '.$e->getMessage(); }
    }
    $masked = stripStringsAndComments($code,$lang);
    $pairs = ['('=>')','['=>']','{'=>'}'];
    $stack=[]; $line=1;
    $chars=preg_split('//u',$masked,-1,PREG_SPLIT_NO_EMPTY) ?: [];
    foreach($chars as $ch){
        if($ch==="\n") {$line++; continue;}
        if(isset($pairs[$ch])) {$stack[]=$ch; continue;}
        if(in_array($ch,array_values($pairs),true)){
            if(!$stack) return "Structural delimiter error near line {$line}: unexpected {$ch}.";
            $open=array_pop($stack);
            if($pairs[$open]!==$ch) return "Structural delimiter error near line {$line}: expected {$pairs[$open]}, found {$ch}.";
        }
    }
    if($stack) return 'Structural delimiter error: unclosed '.$stack[count($stack)-1].' delimiter.';
    return null;
}
function entropy(array $tokens): float {
    $freq=array_count_values($tokens); $n=count($tokens); if(!$n) return 0.0;
    $e=0.0; foreach($freq as $f){$p=$f/$n;$e-=$p*log($p,2);} return $e;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') fatal('POST request required.');
$code=''; $name='pasted.txt';
if(isset($_FILES['file'])){
    if(($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK) fatal('Unreadable upload or upload failure.');
    $name=basename((string)$_FILES['file']['name']);
    $code=file_get_contents($_FILES['file']['tmp_name']);
    if($code===false) fatal('Unable to read uploaded file.');
} else {
    $code=(string)($_POST['code']??'');
    $name=basename((string)($_POST['filename']??'pasted.txt')) ?: 'pasted.txt';
}
if(trim($code)==='') fatal('No source code was supplied.');
if(strlen($code)>10*1024*1024) fatal('File is larger than the 10 MB analysis limit.');

[$langId,$language]=languageFor($name,$LANGS);
$syntax=syntaxHint($code,$langId);
if($syntax!==null) fatal($syntax);

$lines=preg_split('/\R/',$code) ?: [];
$masked=stripStringsAndComments($code,$langId);
$generic=['data','result','item','response','temp','output','obj','payload','value','res','tmp','foo','bar'];
$genericHits=0; foreach($generic as $g) $genericHits += preg_match_all('/(?<![A-Za-z0-9_])'.preg_quote($g,'/').'(?![A-Za-z0-9_])/i',$masked,$m) ?: 0;
$comments=0; $tautological=0;
foreach($lines as $i=>$lt){
    $trim=trim($lt);
    if(preg_match('/^(\/\/|#|--)\s*(.+)$/',$trim,$m)){
        $comments++;
        $commentNorm=preg_replace('/[^a-z0-9]/i','',strtolower($m[2]))??'';
        if($commentNorm!=='' && preg_match('/\b(function|def|class|fn|func|sub|method)\s+([A-Za-z_][A-Za-z0-9_]*)/i',$commentNorm,$x)) $tautological++;
        if($i+1<count($lines) && preg_match('/\b(function|def|class|fn|func|sub)\s+([A-Za-z_][A-Za-z0-9_]*)/i',$lines[$i+1],$x)){
            $n=preg_replace('/[^a-z0-9]/i','',strtolower($x[2]))??'';
            if($n!=='' && str_contains($commentNorm,$n)) $tautological++;
        }
    }
}
$guards=preg_match_all('/\b(if|unless|when)\s*\([^)]*(null|nil|none|true|false|undefined|empty|error)[^)]*\)/i',$masked,$m)?:0;
$fallbacks=preg_match_all('/(\?\?|\b(?:else|default|fallback)\b|\btry\b.*\bcatch\b)/i',$masked,$m)?:0;
$tokens=preg_split('/[^A-Za-z0-9_#$@]+/',$masked,-1,PREG_SPLIT_NO_EMPTY)?:[];
$tokens=array_map('strtolower',$tokens);
$n=count($tokens); $ent=entropy($tokens);

$score=0;
$score += $tautological*20;
$score += min(25,$guards*5);
$score += min(20,$fallbacks*4);
$score += min(20,max(0,$genericHits-2)*3);
if($n>12 && $ent<3.6) $score+=10;
if(count($lines)>30 && $comments===0) $score+=5;
$score=min(100,$score);

if($score<40){$exit=0;$classification='PASS / CLEAN';$tone='Low heuristic risk; no strong AI-style structural signals detected.';}
elseif($score<70){$exit=1;$classification='WARNING / SUSPICIOUS';$tone='Some heuristic patterns were detected; human review is advised.';}
else{$exit=2;$classification='FLAGGED / AI BREACH';$tone='High heuristic concentration of patterns associated with unvetted/generated code.';}

respond(true,[
 'exit_code'=>$exit,'classification'=>$classification,'score'=>$score,'interpretation'=>$tone,
 'file'=>$name,'language'=>$language,'language_id'=>$langId,'lines'=>count($lines),'tokens'=>$n,
 'entropy'=>round($ent,3),'signals'=>array_values(array_filter([
   $tautological?"Tautological/redundant comments: {$tautological}":null,
   $guards?"Defensive guard/default checks: {$guards}":null,
   $fallbacks?"Fallback/error-handling constructs: {$fallbacks}":null,
   $genericHits?"Generic identifier density: {$genericHits}":null
 ])),
 'metrics'=>['tautological_comments'=>$tautological,'defensive_guards'=>$guards,'fallbacks'=>$fallbacks,'generic_identifiers'=>$genericHits]
]);
