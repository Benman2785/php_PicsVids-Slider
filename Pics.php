<?php
$dir = __DIR__;

$files = glob($dir . "/pics/*.{bmp,gif,jpg,jpeg,png,webp}", GLOB_BRACE);
sort($files, SORT_NATURAL | SORT_FLAG_CASE);

if (!$files)
  die("Keine Bild-Dateien gefunden.");

$current = isset($_GET['pic']) ? intval($_GET['pic']) : 0;
$current = max(0, min($current, count($files) - 1));

$self = htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bild Viewer</title>
<style>
  html, body {
    margin: 0;
    height: 100%;
    background-color: #666666;
    overflow: hidden;
  }

  .wrapper {
    position: fixed;
    inset: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
  }

  .inner {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
  }

  #image {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    display: block;
  }

  .arrow {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    font-size: 48px;
    color: #fff;
    text-decoration: none;
    padding: 8px 12px;
    user-select: none;
    cursor: pointer;
    z-index: 50;
  }
  .arrow-left { left: 8px; }
  .arrow-right { right: 8px; }

  .disabled {
    visibility: hidden;
    pointer-events: none;
  }

  @media (max-width: 480px) {
    .arrow { font-size: 36px; padding: 6px; }
  }
</style>
</head>
<body>

<a class="arrow arrow-left <?= ($current == 0) ? 'disabled' : '' ?>"
   href="<?= $self ?>?pic=<?= max(0, $current - 1) ?>" aria-label="Zurück">◀</a>

<div class="wrapper">
  <div class="inner">
    <img id="image" src="pics/<?= urlencode(basename($files[$current])) ?>" alt="Bild">
  </div>
</div>

<a class="arrow arrow-right <?= ($current >= count($files) - 1) ? 'disabled' : '' ?>"
   href="<?= $self ?>?pic=<?= min(count($files) - 1, $current + 1) ?>" aria-label="Vor">▶</a>

<script>
const img = document.getElementById('image');
const inner = document.querySelector('.inner');

function resizeImage() {
  const containerW = inner.clientWidth;
  const containerH = inner.clientHeight;

  const naturalW = img.naturalWidth;
  const naturalH = img.naturalHeight;

  if (!naturalW || !naturalH) return;

  const scaleX = containerW / naturalW;
  const scaleY = containerH / naturalH;
  const scale = Math.min(scaleX, scaleY); // entferne die 1-Begrenzung

  img.style.width = naturalW * scale + 'px';
  img.style.height = naturalH * scale + 'px';
}

// resize, wenn Bild geladen ist oder Fenstergröße sich ändert
img.addEventListener('load', resizeImage);
window.addEventListener('resize', resizeImage);

// Pfeiltasten
window.addEventListener('keydown', function(e) {
  if (e.key === 'ArrowLeft') {
    const left = document.querySelector('.arrow-left:not(.disabled)');
    if (left) window.location.href = left.href;
  } else if (e.key === 'ArrowRight') {
    const right = document.querySelector('.arrow-right:not(.disabled)');
    if (right) window.location.href = right.href;
  }
});
</script>

</body>
</html>