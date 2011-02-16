<?php
  require_once('twitterOAuth.php');
  include 'config.inc';

  require_once('Tweet.class.php');
  require_once('User.class.php');
  require_once('Issue.class.php');

  $my_user_id = 0;
  $ignore_ids = array();

  $thanks_message = 'うべー';


function http_post($url, $data, $content_type='text/html')
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, Array('Content-Type: '.$content_type));
#  curl_setopt($ch, CURLOPT_POSTFIELDS, urlencode($data));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
# curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  curl_close($ch);

  return $response;
}


  $to = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

  $params = array();

  $since_id = Tweet::maxId();
  if ($since_id) $params['since_id'] = $since_id;
// echo 'params: '.print_r($params)."\n";

  $response = $to->oAuthRequest('https://api.twitter.com/statuses/home_timeline.xml', 'GET', $params);
  $xml = simplexml_load_string($response);
  foreach ($xml->status as $status) # ほんとは逆順にしたい
  {
    $reply_msg = null;

    $twuser = $status->user;
    // 自分の場合ループしないように...
    if (isset($ignore_ids[(string)$twuser->id])) continue;
    if (FALSE !== strpos($status->text, '@marugametter')) continue;

    $created_at = time(); // $status->created_at;
    $content = trim( preg_replace('/@marugametter/','',$status->text) );

    printf("%s (%s, %s) <%s> %s\n",
			  $twuser->name, $twuser->screen_name, $twuser->id,
                          $status->id,
			  $content);

    $user = new User($twuser->id,
                     $twuser->name,
                     $twuser->screen_name,
                     $twuser->location,
                     $twuser->description,
                     $twuser->profile_image_url,
                     $twuser->protected);
    $user->save();

    $tweet = new Tweet($status->id,
                       $created_at,
                       $status->text,
                       $status->in_reply_to_status_id,
                       $status->in_reply_to_user_id,
                       $status->user->id);
    $tweet->save();

    if (FALSE !== strpos($content, '#kr_cvs')) {
      if (FALSE !== strpos($content, '梅')) {
        $order = 'しおミルキー';
      } else {
        $order = '梅';
      }
      $reply_msg = $order.' #kr_cvs';
    }
    elseif (FALSE !== strpos($content, '30分')) {
      $reply_msg = '金曜日以外は390円／30分';
    }
    elseif (FALSE !== strpos($content, '風邪')) {
      $reply_msg = '風邪をひいたら梅やで';
    }
    elseif (FALSE !== strpos($content, '腹痛')) {
      $reply_msg = '腹痛には梅やで';
    }

    if ($reply_msg) {
      $result = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml', 'POST',
				 array('status' => sprintf('@%s %s', $user->screenname, $reply_msg),
			               'in_reply_to_status_id' => $status->id) );
    }
  }

  
