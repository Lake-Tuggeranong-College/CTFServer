<?php
session_start();

const BINGO_ADMIN_PASSWORD = 'ChangeThisToAStrongPassword!';
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


if (
    !isset($_SESSION['bingo_board']) ||
    !isset($_SESSION['bingo_owners']['blue']) ||
    !isset($_SESSION['bingo_owners']['red']) ||
    !is_array($_SESSION['bingo_owners']['blue']) ||
    !is_array($_SESSION['bingo_owners']['red'])
) {
    $ids = range(0, 24);
    shuffle($ids);

    $_SESSION['bingo_board'] = $ids;
    $_SESSION['bingo_owners'] = [
        'blue' => array_fill(0, 25, false),
        'red'  => array_fill(0, 25, false),
    ];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_game'])) {
        $password = $_POST['admin_password'] ?? '';

        if (
            !is_string($password) ||
            !hash_equals(BINGO_ADMIN_PASSWORD, $password)
        ) {
            $_SESSION['bingo_message'] = 'Incorrect admin password.';
        } else {
            $ids = range(0, 24);
            shuffle($ids);

            $_SESSION['bingo_board'] = $ids;
            $_SESSION['bingo_owners'] = [
                'blue' => array_fill(0, 25, false),
                'red'  => array_fill(0, 25, false),
            ];

            $_SESSION['bingo_message'] = 'A new board has been generated!';
        }
    } elseif (isset($_POST['square'], $_POST['team'], $_POST['flag'])) {
        $i = filter_var($_POST['square'], FILTER_VALIDATE_INT);
        $team = $_POST['team'];
        $flag = $_POST['flag'];

        if (
            $i === false ||
            $i < 0 ||
            $i > 24 ||
            !in_array($team, ['blue', 'red'], true)
        ) {
            $_SESSION['bingo_message'] = 'Invalid submission.';
        } elseif ($_SESSION['bingo_owners'][$team][$i]) {
            $_SESSION['bingo_message'] =
                'Your team has already claimed this square.';
        } elseif (
            !is_string($flag) ||
            !preg_match('/\ACTF\{[^{}\r\n]+\}\z/', $flag)
        ) {
            $_SESSION['bingo_message'] = 'Use the format CTF{XXX}.';
        } else {
            $challengeId = $_SESSION['bingo_board'][$i];

            if (hash_equals($challenges[$challengeId][1], $flag)) {
                $_SESSION['bingo_owners'][$team][$i] = true;

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

$blue = count(array_filter($owners['blue']));
$red  = count(array_filter($owners['red']));


function bingo0(array $owners, string $team): int {
    $count = 0;

    for ($r = 0; $r < 5; $r++) {
        $row = true;
        $col = true;

        for ($c = 0; $c < 5; $c++) {
            if (!$owners[$team][$r * 5 + $c]) $row = false;
            if (!$owners[$team][$c * 5 + $r]) $col = false;
        }

        if ($row) $count++;
        if ($col) $count++;
    }

    foreach ([[0,6,12,18,24], [4,8,12,16,20]] as $diagonal) {
        $line = true;

        foreach ($diagonal as $i) {
            if (!$owners[$team][$i]) $line = false;
        }

        if ($line) $count++;
    }

    return $count;
}


$owners = $_SESSION['bingo_owners'];

$blue = count(array_filter($owners['blue']));
$red  = count(array_filter($owners['red']));
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
.cell.both {
    background: linear-gradient(135deg, #103459 50%, #421e2a 50%);
    border: 2px solid #b5a5ff;
}

.cell.both .owner {
    color: #e0d5ff;
}
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

<form method="post" id="newGameForm">
    <label for="adminPassword" class="sr-only">Admin password</label>
    <input
        type="password"
        name="admin_password"
        id="adminPassword"
        placeholder="Admin password"
        autocomplete="current-password"
        required
    >
    <button
        class="new-game"
        type="submit"
        name="new_game"
        value="1"
        onclick="return confirm('Reset scores and shuffle the board?')"
    >↻ New Game</button>
</form>

</div>
<style>
#newGameForm {
    display: flex;
    align-items: center;
    gap: 8px;
}

#newGameForm input {
    width: 180px;
    margin-top: 0;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>


<div class="board">
<?php foreach ($_SESSION['bingo_board'] as $i => $id):
    $blueClaimed = $owners['blue'][$i];
    $redClaimed  = $owners['red'][$i];

    $classes = 'cell';
    if ($blueClaimed) $classes .= ' blue';
    if ($redClaimed)  $classes .= ' red';
    if ($blueClaimed && $redClaimed) $classes .= ' both';
?>
    <button
        type="button"
        class="<?= h($classes) ?>"
        onclick="openChallenge(<?= $i ?>)"
    >
        <span class="num">SQUARE <?= $i + 1 ?></span>
        <span class="name"><?= h($challenges[$id][0]) ?></span>

        <?php if ($blueClaimed || $redClaimed): ?>
            <span class="owner">
                <?php if ($blueClaimed && $redClaimed): ?>
                    BLUE + RED
                <?php elseif ($blueClaimed): ?>
                    BLUE TEAM
                <?php else: ?>
                    RED TEAM
                <?php endif; ?>
            </span>
        <?php endif; ?>
    </button>
<?php endforeach; ?>
</div>

<footer>
    CTF Bingo &mdash; Capture squares and complete your lines!
</footer>
</div>

<dialog id="challengeDialog">
    <h2 id="challengeTitle">Submit a Flag</h2>
    <p class="hint">Solve the challenge and enter its flag to claim this square.</p>

    <form method="post" id="challengeForm">
        <input type="hidden" name="square" id="squareInput">

        <label for="teamInput">Choose your team
            <select name="team" id="teamInput" required>
                <option value="blue">Blue Team</option>
                <option value="red">Red Team</option>
            </select>
        </label>

        <label for="flagInput">Flag
            <input
                type="text"
                name="flag"
                id="flagInput"
                placeholder="CTF{YOUR_FLAG}"
                pattern="CTF\{[^{}\r\n]+\}"
                maxlength="200"
                required
                autocomplete="off"
                spellcheck="false"
            >
        </label>

        <div class="actions">
            <button type="button" id="cancelButton">Cancel</button>
            <button type="submit" class="submit">Submit Flag</button>
        </div>
    </form>
</dialog>

<script>
const challengeNames = <?= json_encode(
    array_map(
        fn($id) => $challenges[$id][0],
        $_SESSION['bingo_board']
    ),
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;

const dialog = document.getElementById('challengeDialog');
const form = document.getElementById('challengeForm');
const squareInput = document.getElementById('squareInput');
const title = document.getElementById('challengeTitle');
const flagInput = document.getElementById('flagInput');

function openChallenge(index) {
    squareInput.value = index;
    title.textContent = challengeNames[index];
    flagInput.value = '';
    dialog.showModal();
    flagInput.focus();
}

document.getElementById('cancelButton').addEventListener('click', () => {
    dialog.close();
});

dialog.addEventListener('click', (event) => {
    if (event.target === dialog) {
        dialog.close();
    }
});

form.addEventListener('submit', (event) => {
    if (!form.reportValidity()) {
        event.preventDefault();
    }
});
</script>
</body>
</html>