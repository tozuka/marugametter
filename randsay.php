<?php
  require_once('randlang.php');

  $pattern_id = count($argv) > 1 ? $argv[1] : rand(1,7);
  $pattern_id = 0;

  $msg = null;
  switch ($pattern_id) {
    case 0: $msg = generate_marukame(); break;
    case 1: $msg = generate_tetete(); break;
    case 2: $msg = generate_tetete_ja(); break;
    case 3: $msg = generate_ebiebi(); break;
    case 4: $msg = generate_morus(); break;
    case 5: $msg = generate_sansuu(); break;
    case 6: $msg = generate_melody(); break;
    case 7: $msg = generate_poke(); break;
    default: $msg = null; break;
  }
  if ($msg) echo $msg."\n";

