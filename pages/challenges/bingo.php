
<?php
session_start();

$challenges = [
    ['Web Exploitation', 'CTF{WEB_EXPLOIT}'],
    ['SQL Injection', 'CTF{SQL_INJECTION}'],
    ['Caesar Cipher', 'CTF{CAESAR}'],
    ['Base64 Decode', 'CTF{BASE64}'],
    ['Hidden Directory', 'CTF{HIDDEN_DIR}'],
    ['File Metadata', 'CTF{METADATA}'],
    ['Cookie Tampering', 'CTF{COOKIE}'],
    ['ROT13', 'CTF{ROT13}'],
    ['Robots.txt', 'CTF{ROBOTS}'],
    ['Source Code', 'CTF{SOURCE_CODE}'],
    ['Hex Encoding', 'CTF{HEX}'],
    ['HTTP Headers', 'CTF{HEADERS}'],
    ['JavaScript Clue', 'CTF{JAVASCRIPT}'],
    ['XSS Challenge', 'CTF{XSS}'],
    ['SSH Investigation', 'CTF{SSH}'],
    ['DNS Lookup', 'CTF{DNS}'],
    ['Image Stego', 'CTF{STEGANOGRAPHY}'],
    ['Log Analysis', 'CTF{LOGS}'],
    ['File Permissions', 'CTF{PERMISSIONS}'],
    ['Archive Analysis', 'CTF{ARCHIVE}'],
    ['Git History', 'CTF{GIT}'],
    ['Hash Identification', 'CTF{HASH}'],
    ['URL Encoding', 'CTF{URL_ENCODING}'],
    ['Password Cracking', 'CTF{PASSWORD}'],
    ['Final Challenge', 'CTF{FINAL_BOSS}'],
];

if (!isset($_SESSION['bingo_board'])) {
    $ids = range(0, 24);
    shuffle($ids);
    $_SESSION['bingo_board'] = $ids;
    $_SESSION['bingo_owners'] = array_fill(0, 25, '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_game'])) {
        $ids = range(0, 24);
        shuffle($ids);
        $_SESSION['bingo_board'] = $ids;
        $_SESSION['bingo_owners'] = array_fill(0, 25, '');
        $_SESSION['bingo_message'] = 'A new board has been generated!';
    } elseif (isset($_POST['square'], $_POST['team'], $_POST['flag'])) {
        $i = filter_var($_POST['square'], FILTER_VALIDATE_INT);
        $team = $_POST['team'];
        $flag = $_POST['flag'];

        if ($i === false || $i < 0 || $i > 24 ||
            !in_array($team, ['blue', 'red'], true)) {
            $_SESSION['bingo_message'] = 'Invalid submission.';
        } elseif ($_SESSION['bingo_owners'][$i] !== '') {
            $_SESSION['bingo_message'] = 'This square is already claimed.';
        } elseif (!is_string($flag) ||
            !preg_match('/\ACTF\{[^{}\r\n]+\}\z/', $flag)) {
            $_SESSION['bingo_message'] = 'Use the format CTF{XXX}.';
        } else {
            $challengeId = $_SESSION['bingo_board'][$i];
            if (hash_equals($challenges[$challengeId][1], $flag)) {
                $_SESSION['bingo_owners'][$i] = $team;
                $_SESSION['bingo_message'] =
                    strtoupper($team) . ' TEAM CLAIMED THE SQUARE!';
            } else {
                $_SESSION['bingo_message'] = 'Incorrect flag!';
            }
        }
    }

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$owners = $_SESSION['bingo_owners'];

function bingo0(array $owners, string $team): int {
    $count = 0;

    for ($r = 0; $r < 5; $r++) {
        $line = true;
        for ($c = 0; $c < 5; $c++) {
            if ($owners[$r * 5 + $c] !== $team) $line = false;
        }
        if ($line) $count++;

        $line = true;
        for ($c = 0; $c < 5; $c++) {
            if ($owners[$c * 5 + $r] !== $team) $line = false;
        }
        if ($line) $count++;
    }

    foreach ([[0,6,12,18,24], [4,8,12,16,20]] as $diagonal) {
        $line = true;
        foreach ($diagonal as $i) {
            if ($owners[$i] !== $team) $line = false;
        }
        if ($line) $count++;
    }

    return $count;
}

$blue = count(array_filter($owners, fn($v) => $v === 'blue'));
$red = count(array_filter($owners, fn($v) => $v === 'red'));
$message = $_SESSION['bingo_message'] ?? '';
unset($_SESSION['bingo_message']);

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CTF Bingo</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#0b1020;color:#edf3ff;font-family:Arial,sans-serif;padding:28px 16px}
.container{max-width:950px;margin:auto}
header{text-align:center;margin-bottom:25px}
h1{font-size:clamp(2rem,5vw,3rem);margin:0;color:#fff;letter-spacing:2px}
header p{color:#93a4c3}
.scores{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px}
.score{padding:18px;border-radius:12px;background:#141e32;border:1px solid #293853}
.score.blue{border-top:4px solid #329cff}
.score.red{border-top:4px solid #ff5364}
.score h2{font-size:1rem;margin:0 0 8px}
.score strong{font-size:2rem}
.score p{color:#a7b5cd;margin:5px 0 0}
.toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}
button{font:inherit;cursor:pointer}
.new-game{border:0;border-radius:8px;padding:11px 16px;background:#293853;color:white}
.board{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px}
.cell{aspect-ratio:1;min-width:0;padding:7px;background:#141e32;color:#eaf1ff;border:1px solid #293853;border-radius:10px;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:8px;text-align:center;overflow-wrap:anywhere}
.cell:hover:not(:disabled){border-color:#91b6ff;background:#1b2943}
.cell.blue{background:#103459;border:2px solid #329cff}
.cell.red{background:#421e2a;border:2px solid #ff5364}
.cell:disabled{opacity:1;cursor:default}
.cell .num{font-size:.68rem;color:#96a8c7}
.cell .name{font-size:clamp(.62rem,1.5vw,.95rem);font-weight:bold}
.cell .owner{font-size:.7rem;font-weight:bold}
.blue .owner{color:#65b8ff}.red .owner{color:#ff8793}
.message{padding:12px;border-radius:8px;background:#192941;margin-bottom:15px}
dialog{width:min(400px,calc(100% - 24px));background:#141e32;color:white;border:1px solid #40516f;border-radius:14px;padding:24px}
dialog::backdrop{background:#000b}
dialog h2{margin-top:0}
label{display:block;margin:16px 0;color:#b9c7df}
input,select{display:block;width:100%;padding:12px;margin-top:7px;background:#0b1020;color:white;border:1px solid #40516f;border-radius:8px}
.actions{display:flex;gap:10px}
.actions button{width:50%;padding:11px;border:0;border-radius:8px;color:white;background:#293853}
.actions .submit{background:#167bd0}
.hint{color:#a7b5cd;font-size:.85rem}
footer{text-align:center;color:#899ab8;font-size:.8rem;margin-top:22px}
@media(max-width:500px){body{padding:18px 8px}.board{gap:4px}.cell{padding:3px;gap:4px;border-radius:5px}.cell .num,.cell .owner{font-size:.55rem}.toolbar{align-items:flex-start}.new-game{padding:9px;font-size:.85rem}}
</style>
</head>
<body>
<div class="container">
<header>
<h1>⚑ CTF BINGO</h1>
<p>Capture squares. Complete lines. Win the battle.</p>
</header>

<?php if ($message !== ''): ?>
<div class="message" role="status"><?= h($message) ?></div>
<?php endif; ?>

<div class="scores">
<div class="score blue">
<h2>🔵 BLUE TEAM</h2>
<strong><?= $blue ?></strong> <span>/ 25 squares</span>
<p><?= bingo0($owners,'blue') ?> completed bingo lines</p>
</div>
<div class="score red">
<h2>🔴 RED TEAM</h2>
<strong><?= $red ?></strong> <span>/ 25 squares</span>
<p><?= bingo0($owners,'red') ?> completed bingo lines</p>
</div>
</div>

<div class="toolbar">
<span class="hint">Click an unclaimed square to submit a flag.</span>
<form method="post" onsubmit="return confirm('Reset scores and shuffle the board?')">
<button class="new-game" type="submit" name="new_game" value="1">↻ New Game</button>
</form>
</div>

<div class="board">
<?php foreach ($_SESSION['bingo_board'] as $i => $id):
    $owner = $owners[$i];
?>
<button type="button"
class="cell <?= h($owner) ?>"
data-i="<?= $i ?>"
data-title="<?= h($challenges[$id][0]) ?>"
<?= $owner !== '' ? 'disabled' : '' ?>>
<span class="num">SQUARE <?= $i+1 ?></span>
<span class="name"><?= h($challenges[$id][0]) ?></span>
<?php if ($owner !== ''): ?>
<span class="owner"><?= strtoupper(h($owner)) ?> CLAIMED</span>
<?php else: ?>
<span class="num">UNCLAIMED</span>
<?php endif; ?>
</button>
<?php endforeach; ?>
</div>

<footer>One bingo = five owned squares in a row, column or diagonal.</footer>
</div>

<dialog id="claimDialog">
<h2 id="challengeTitle">Claim square</h2>
<p class="hint">Enter the exact flag found during the challenge.</p>
<form method="post">
<input type="hidden" name="square" id="squareIndex">
<label>Choose team
<select name="team" required>
<option value="blue">🔵 Blue Team</option>
<option value="red">🔴 Red Team</option>
</select>
</label>
<label>CTF Flag
<input name="flag" required pattern="CTF\{[^{}\r\n]+\}"
placeholder="CTF{YOUR_FLAG}" autocomplete="off" spellcheck="false">
</label>
<div class="actions">
<button type="button" id="cancel">Cancel</button>
<button type="submit" class="submit">Claim Square</button>
</div>
</form>
</dialog>

<script>
const dlg = document.getElementById('claimDialog');
document.querySelectorAll('.cell:not(:disabled)').forEach(cell => {
    cell.addEventListener('click', () => {
        document.getElementById('squareIndex').value = cell.dataset.i;
        document.getElementById('challengeTitle').textContent = cell.dataset.title;
        dlg.showModal();
    });
});
document.getElementById('cancel').addEventListener('click', () => dlg.close());
</script>
</body>
</html>