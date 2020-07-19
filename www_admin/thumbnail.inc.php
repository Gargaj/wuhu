<?php
function thumbnail_shrink($srcfile, $dstfile, $limitx=128,$limity=128)
{
  list($x,$y,$type,$attr) = GetImageSize($srcfile);
  if ($x<=$limitx && $y<=$limity) {
    copy($srcfile, $dstfile);
    return;
  }

  $big = $x>$y ? $x : $y;
  $newx = floor($x*$limitx/$big);
  $newy = floor($y*$limity/$big);

  $openfunc = Array(
    1 =>"imagecreatefromgif",
    2 =>"imagecreatefromjpeg",
    3 =>"imagecreatefrompng",
  );

  $src = $openfunc[$type]($srcfile);
  $dst = imagecreatetruecolor($newx, $newy);

  $result = imagecopyresampled($dst, $src, 0, 0, 0, 0, $newx, $newy, $x, $y);

  imagepng($dst, $dstfile);
  imagedestroy($dst);
  imagedestroy($src);
}

function thumbnail_crop($srcfile, $dstfile, $limitx=128,$limity=128)
{
  list($x,$y,$type,$attr) = GetImageSize($srcfile);

  $aspThmb = $limitx / (float)$limity;
  $aspOrig = $x / (float)$y;

  /*
    this might need some explanation:

    we need to decide if the aspect ratios of the two pictures are the same.

    aspect ratios are the same when they're both on the same side of 1,
    i.e. they're both smaller than 1 (portrait) or both bigger than 1 (landscape)

    so we do this by subtracting 1 from each (causing portrait ratios to be negative)
    and multiplying them: if only one of them is negative, the result will be < 0

    if one of them is square (1), we assume they're not the same aspect.

    we could probably optimize one more branch but meh.
  */

  if (($aspThmb - 1) * ($aspOrig - 1) <= 0)
  {
    // aspects/orientation are different
    if ($aspThmb > $aspOrig)
    {
      $cropx = $x;
      $cropy = floor($x * $limity / $limitx);
    }
    else
    {
      $cropx = floor($y * $limitx / $limity);
      $cropy = $y;
    }
  }
  else
  {
    // aspects match
    if ($aspThmb < $aspOrig)
    {
      $cropx = floor($y * $limitx / $limity);
      $cropy = $y;
    }
    else
    {
      $cropx = $x;
      $cropy = floor($x * $limity / $limitx);
    }
  }

  $openfunc = Array(
    1 =>"imagecreatefromgif",
    2 =>"imagecreatefromjpeg",
    3 =>"imagecreatefrompng",
  );

  $src = $openfunc[$type]($srcfile);
  $dst = imagecreatetruecolor($limitx, $limity);

  $result = imagecopyresampled($dst, $src, 0, 0, ($x - $cropx) / 2, ($y - $cropy) / 2, $limitx, $limity, $cropx, $cropy);

  imagepng($dst, $dstfile);
  imagedestroy($dst);
  imagedestroy($src);
}

function thumbnail($srcfile, $dstfile, $limitx=128,$limity=128)
{
  return thumbnail_crop($srcfile, $dstfile, $limitx, $limity);
}
?>