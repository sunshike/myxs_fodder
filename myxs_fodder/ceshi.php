<?php
$cmd = 'ffmpeg -i 01.mp4 -i 01.png -filter_complex overlay 03.mp4';

exec($cmd);