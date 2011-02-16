<?php
  require_once('twitterOAuth.php');
  require_once('randlang.php');
  include 'config.inc';
  $hmm = idate('H')*100 + idate('i');

  ## freq = 20/60 (cronjob per hour) * 1/100 (rand) * 7 = 7/300 (/hour) = 300/7 [= 40h+毎]
  $msg = null;
  if (1100 <= $hmm && $hmm <= 2130) $msg = generate_marukame();

  if ($msg) {
    echo "(tweet) ".$msg."\n";
    $to = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
    $content = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml', 'POST',
                                 array('status' => $msg));
  }
  else {
    echo "(do nothing)\n";
  }

