<?php
/** SesliOfis v3.9.10.186.8.3 — Supplier Popup Hard Fix Rollback */
const SO_V18683_RB_KEY = 'sesliofis391018683';
function so18683rb_h($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function so18683rb_page($title,$body,$ok=false){
    $color=$ok?'#167347':'#b42318';
    echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.so18683rb_h($title).'</title></head><body style="font-family:Arial,sans-serif;background:#f4f7fa;padding:24px;color:#10233f"><main style="max-width:760px;margin:auto;background:#fff;border:1px solid #dce5ee;border-radius:16px;padding:24px"><h1 style="color:'.$color.'">'.so18683rb_h($title).'</h1>'.$body.'</main></body></html>';
    exit;
}
function so18683rb_fail($m){so18683rb_page('Rollback durduruldu','<p>'.so18683rb_h($m).'</p>',false);}
function so18683rb_atomic($path,$content){
    $tmp=$path.'.tmp.'.bin2hex(random_bytes(5));
    if(file_put_contents($tmp,$content,LOCK_EX)===false){throw new RuntimeException('Geçici dosya yazılamadı: '.$path);}
    @chmod($tmp,0644);
    if(!@rename($tmp,$path)){@unlink($tmp);throw new RuntimeException('Dosya atomik değiştirilemedi: '.$path);}
}
$key=isset($_GET['key'])?(string)$_GET['key']:'';
if(!hash_equals(SO_V18683_RB_KEY,$key)){so18683rb_fail('Geçersiz rollback anahtarı.');}
$root=realpath(__DIR__);
if($root===false||basename($root)!=='app.sesliofis.com'){so18683rb_fail('Dosya app.sesliofis.com kökünde olmalıdır.');}
$manifestFile=$root.'/_rollback_v391018683_supplier_popup.json';
if(!is_file($manifestFile)){so18683rb_fail('Rollback manifesti bulunamadı.');}
$raw=file_get_contents($manifestFile);
$data=$raw!==false?json_decode($raw,true):null;
if(!is_array($data)||empty($data['files'])){so18683rb_fail('Rollback manifesti geçersiz.');}
$restored=array();
try{
    foreach($data['files'] as $item){
        $live=isset($item['live'])?(string)$item['live']:'';
        $backup=isset($item['backup'])?(string)$item['backup']:'';
        if($live===''||$backup===''||!is_file($backup)){throw new RuntimeException('Yedek dosya eksik: '.basename($backup));}
        $content=file_get_contents($backup);
        if($content===false){throw new RuntimeException('Yedek okunamadı: '.basename($backup));}
        so18683rb_atomic($live,$content);
        $restored[]=basename($live);
    }
    @unlink($manifestFile);
}catch(Throwable $e){so18683rb_fail($e->getMessage());}
so18683rb_page('Rollback tamamlandı','<p>Geri yüklenen dosyalar:</p><ul><li>'.implode('</li><li>',array_map('so18683rb_h',$restored)).'</li></ul><p><a href="/products/new">Ürün ekleme ekranını kontrol et</a></p>',true);
