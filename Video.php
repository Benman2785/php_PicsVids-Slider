<?php
$dir = __DIR__;

$files = glob($dir . "/*.{mp4,webm}", GLOB_BRACE);
sort($files, SORT_NATURAL | SORT_FLAG_CASE);

if (!$files)
  die("Keine Video-Dateien gefunden.");

$current = isset($_GET['vid']) ? intval($_GET['vid']) : 0;
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
    html,
    body {
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
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    #video {
      max-width: 100%;
      max-height: 100%;
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

    .arrow-left {
      left: 8px;
    }

    .arrow-right {
      right: 8px;
    }

    .disabled {
      visibility: hidden;
      pointer-events: none;
    }

    @media (max-width: 480px) {
      .arrow {
        font-size: 36px;
        padding: 6px;
      }
    }
  </style>
</head>

<body>

  <a class="arrow arrow-left <?= ($current == 0) ? 'disabled' : '' ?>"
    href="<?= $self ?>?vid=<?= max(0, $current - 1) ?>" aria-label="Zurück">◀</a>

  <div class="wrapper">
    <div class="inner">
      <video id="vid" controls autoplay style="max-width:100%; max-height:100%;">
        <source src="<?= htmlspecialchars(basename($files[$current])) ?>" type="video/mp4">
        Dein Browser unterstützt dieses Videoformat nicht.
      </video>
    </div>
  </div>

  <a class="arrow arrow-right <?= ($current >= count($files) - 1) ? 'disabled' : '' ?>"
    href="<?= $self ?>?vid=<?= min(count($files) - 1, $current + 1) ?>" aria-label="Vor">▶</a>

  <script>
    const vid = document.getElementById('vid');
    const inner = document.querySelector('.inner');

    function resizeVideo() {
      const containerW = inner.clientWidth;
      const containerH = inner.clientHeight;

      const videoW = vid.videoWidth;
      const videoH = vid.videoHeight;

      if (!videoW || !videoH) return;

      const scaleX = containerW / videoW;
      const scaleY = containerH / videoH;
      const scale = Math.min(scaleX, scaleY);

      vid.style.width = videoW * scale + 'px';
      vid.style.height = videoH * scale + 'px';
    }

    vid.addEventListener('loadedmetadata', resizeVideo);
    window.addEventListener('resize', resizeVideo);


    // Pfeiltasten
    window.addEventListener('keydown', function (e) {
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
