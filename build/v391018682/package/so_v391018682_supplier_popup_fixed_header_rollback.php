<?php
/** SesliOfis v3.9.10.186.8.2 rollback */
const SO_V18682_RB_KEY='sesliofis391018682';
function so18682rb_h($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function so18682rb_page($title,$body,$ok=false){$c=$ok?'#167347':'#b42318';echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.so18682rb_h($title).'</title></head><body style="font-family:Arial,sans-serif;background:#f4f7fa;padding:24px"><main style="max-width:760px;margin:auto;background:#fff;border:1px solid #dce5ee;border-radius:16px;padding:24px"><h1 style="color:'.$c.'">'.so18682rb_h($title).'</h1>'.$body.'</main></body></html>';exit;}
$key=isset($_GET['key'])?(string)$_GET['key']:'';
if(!hash_equals(SO_V18682_RB_KEY,$key)){so18682rb_page('Rollback durduruldu','<p>Geçersiz anahtar.</p>');}
$root=realpath(__DIR__);
if($root===false||basename($root)!=='app.sesliofis.com'){so18682rb_page('Rollback durduruldu','<p>Yanlış kök dizin.</p>');}
$manifestFile=$root.'/_rollback_v391018682_supplier_popup.json';
if(!is_file($manifestFile)){so18682rb_page('Rollback durduruldu','<p>Rollback manifesti bulunamadı.</p>');}
$data=json_decode((string)file_get_contents($manifestFile),true);
if(!is_array($data)||empty($data['files'])){so18682rb_page('Rollback durduruldu','<p>Manifest geçersiz.</p>');}
foreach($data['files'] as $f){
    $live=isset($f['live'])?$f['live']:'';
    $backup=isset($f['backup'])?$f['backup']:'';
    if(!$live||!$backup||!is_file($backup)){so18682rb_page('Rollback durduruldu','<p>Yedek dosya eksik.</p>');}
    $tmp=$live.'.rb.'.bin2hex(random_bytes(4));
    if(!copy($backup,$tmp)||!rename($tmp,$live)){@unlink($tmp);so18682rb_page('Rollback durduruldu','<p>Dosya geri yüklenemedi: '.so18682rb_h(basename($live)).'</p>');}
}
@unlink($manifestFile);
so18682rb_page('Rollback tamamlandı','<p>v3.9.10.186.8.2 tedarikçi popup düzeltmesi geri alındı.</p>',true);
