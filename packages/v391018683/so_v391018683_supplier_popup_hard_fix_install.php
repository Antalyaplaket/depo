<?php
/** SesliOfis v3.9.10.186.8.3 — Supplier Popup Hard Fix */
const SO_V18683_KEY = 'sesliofis391018683';
const SO_V18683_PRODUCT_WRITE_SHA = 'c919d0300733a7b0887eca96c19fdf5e08a81aabe00c7919c863e5de1e5d510e';
const SO_V18683_CSS_START = 'SO_SUPPLIER_POPUP_V391018683_CSS_START';
const SO_V18683_CSS_END = 'SO_SUPPLIER_POPUP_V391018683_CSS_END';
const SO_V18683_JS_START = 'SO_SUPPLIER_POPUP_V391018683_JS_START';
const SO_V18683_JS_END = 'SO_SUPPLIER_POPUP_V391018683_JS_END';

function so18683_h($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function so18683_page($title,$body,$ok=false){
    $color=$ok?'#167347':'#b42318';
    echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.so18683_h($title).'</title></head><body style="font-family:Arial,sans-serif;background:#f4f7fa;padding:24px;color:#10233f"><main style="max-width:780px;margin:auto;background:#fff;border:1px solid #dce5ee;border-radius:16px;padding:24px"><h1 style="color:'.$color.'">'.so18683_h($title).'</h1>'.$body.'</main></body></html>';
    exit;
}
function so18683_fail($m){so18683_page('Kurulum durduruldu','<p>'.so18683_h($m).'</p>',false);}
function so18683_atomic($path,$content){
    $tmp=$path.'.tmp.'.bin2hex(random_bytes(5));
    if(file_put_contents($tmp,$content,LOCK_EX)===false){throw new RuntimeException('Geçici dosya yazılamadı: '.$path);}
    @chmod($tmp,0644);
    if(!@rename($tmp,$path)){@unlink($tmp);throw new RuntimeException('Dosya atomik değiştirilemedi: '.$path);}
}
function so18683_json($path,array $data){
    $json=json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    if($json===false){throw new RuntimeException('Manifest oluşturulamadı.');}
    so18683_atomic($path,$json);
}
function so18683_strip_block($content,$start,$end){
    $pattern='~/\*\s*'.preg_quote($start,'~').'\s*\*/.*?/\*\s*'.preg_quote($end,'~').'\s*\*/\s*~s';
    return preg_replace($pattern,'',$content);
}
function so18683_bust_asset($content,$filename,$version,&$count){
    $pattern='~'.preg_quote($filename,'~').'(?:\?[^\"\'\s<>]*)?~';
    return preg_replace($pattern,$filename.'?v='.$version,$content,-1,$count);
}

$key=isset($_GET['key'])?(string)$_GET['key']:'';
if(!hash_equals(SO_V18683_KEY,$key)){so18683_fail('Geçersiz installer anahtarı.');}
$root=realpath(__DIR__);
if($root===false||basename($root)!=='app.sesliofis.com'){so18683_fail('ZIP doğrudan app.sesliofis.com köküne açılmalıdır.');}

$productWrite=$root.'/product_write.php';
$cssFile=$root.'/product_types_v39101865.css';
$jsFile=$root.'/product_types_v39101865.js';
$patchCss=$root.'/supplier_modal_layer_v391018683.css';
$patchJs=$root.'/supplier_modal_layer_v391018683.js';
$manifestFile=$root.'/_rollback_v391018683_supplier_popup.json';

foreach(array($productWrite,$cssFile,$jsFile,$patchCss,$patchJs) as $required){
    if(!is_file($required)){so18683_fail('Gerekli dosya bulunamadı: '.basename($required));}
}
$liveSha=hash_file('sha256',$productWrite);
if(!hash_equals(SO_V18683_PRODUCT_WRITE_SHA,$liveSha)){so18683_fail('Safe-point uyuşmuyor. product_write.php SHA-256: '.$liveSha);}

$product=file_get_contents($productWrite);
$css=file_get_contents($cssFile);
$js=file_get_contents($jsFile);
$newCss=file_get_contents($patchCss);
$newJs=file_get_contents($patchJs);
if($product===false||$css===false||$js===false||$newCss===false||$newJs===false){so18683_fail('Canlı veya payload dosyaları okunamadı.');}
if(strpos($css,SO_V18683_CSS_START)!==false&&strpos($js,SO_V18683_JS_START)!==false&&strpos($product,'product_types_v39101865.css?v=391018683')!==false&&strpos($product,'product_types_v39101865.js?v=391018683')!==false){
    so18683_page('Zaten kurulu','<p>v3.9.10.186.8.3 tedarikçi popup düzeltmesi daha önce kurulmuş.</p><p><a href="/products/new">Ürün ekleme ekranını test et</a></p>',true);
}

$css=so18683_strip_block($css,'SO_SUPPLIER_POPUP_V391018682_CSS_START','SO_SUPPLIER_POPUP_V391018682_CSS_END');
$js=so18683_strip_block($js,'SO_SUPPLIER_POPUP_V391018682_JS_START','SO_SUPPLIER_POPUP_V391018682_JS_END');
$css=so18683_strip_block($css,SO_V18683_CSS_START,SO_V18683_CSS_END);
$js=so18683_strip_block($js,SO_V18683_JS_START,SO_V18683_JS_END);

$mergedCss=rtrim($css)."\n\n/* ".SO_V18683_CSS_START." */\n".trim($newCss)."\n/* ".SO_V18683_CSS_END." */\n";
$mergedJs=rtrim($js)."\n\n/* ".SO_V18683_JS_START." */\n".trim($newJs)."\n/* ".SO_V18683_JS_END." */\n";

$cssRefCount=0;
$jsRefCount=0;
$updatedProduct=so18683_bust_asset($product,'product_types_v39101865.css','391018683',$cssRefCount);
$updatedProduct=so18683_bust_asset($updatedProduct,'product_types_v39101865.js','391018683',$jsRefCount);
if($cssRefCount<1||$jsRefCount<1){so18683_fail('Ürün formunda CSS/JS asset referansları bulunamadı. Hiçbir dosya değiştirilmedi.');}

$dataRoot=dirname($root);
$backupRoot=$dataRoot.'/_app_backups';
if(!is_dir($backupRoot)&&!mkdir($backupRoot,0750,true)&&!is_dir($backupRoot)){so18683_fail('Yedek klasörü oluşturulamadı.');}
$backupDir=$backupRoot.'/supplier_popup_v391018683_'.date('Ymd_His').'_'.substr(bin2hex(random_bytes(5)),0,10);
if(!mkdir($backupDir,0750,true)){so18683_fail('Sürüm yedek klasörü oluşturulamadı.');}
$backupProduct=$backupDir.'/'.basename($productWrite);
$backupCss=$backupDir.'/'.basename($cssFile);
$backupJs=$backupDir.'/'.basename($jsFile);
if(!copy($productWrite,$backupProduct)||!copy($cssFile,$backupCss)||!copy($jsFile,$backupJs)){so18683_fail('Canlı ürün formu/CSS/JS yedeklenemedi.');}

$manifest=array(
    'version'=>'v3.9.10.186.8.3',
    'created_at'=>date('c'),
    'backup_dir'=>$backupDir,
    'files'=>array(
        array('live'=>$productWrite,'backup'=>$backupProduct,'sha256_before'=>hash_file('sha256',$productWrite)),
        array('live'=>$cssFile,'backup'=>$backupCss,'sha256_before'=>hash_file('sha256',$cssFile)),
        array('live'=>$jsFile,'backup'=>$backupJs,'sha256_before'=>hash_file('sha256',$jsFile))
    )
);

try{
    so18683_atomic($cssFile,$mergedCss);
    so18683_atomic($jsFile,$mergedJs);
    so18683_atomic($productWrite,$updatedProduct);
    $manifest['sha256_after']=array(
        'product_write'=>hash_file('sha256',$productWrite),
        'css'=>hash_file('sha256',$cssFile),
        'js'=>hash_file('sha256',$jsFile)
    );
    so18683_json($manifestFile,$manifest);
    @unlink($patchCss);
    @unlink($patchJs);
}catch(Throwable $e){
    if(is_file($backupProduct)){@copy($backupProduct,$productWrite);}
    if(is_file($backupCss)){@copy($backupCss,$cssFile);}
    if(is_file($backupJs)){@copy($backupJs,$jsFile);}
    so18683_fail('Kurulum geri alındı: '.$e->getMessage());
}

so18683_page('Kurulum tamamlandı','<p>Tedarikçi Seç ve Yeni Tedarikçi Ekle popupları için gerçek ürün formu CSS/JS dosyaları güncellendi ve tarayıcı önbelleğini kırmak için asset sürümü <strong>391018683</strong> olarak yenilendi.</p><ul><li>Fixed header üstü yüksek katman</li><li>320–430 px mobil genişlik kilidi</li><li>Stacking-context atası sıfırlama</li><li>Uzun isim ve input taşma koruması</li><li>Arka sayfa kaydırma kilidi</li><li>iPhone/PWA cache-busting</li></ul><p><a href="/products/new">Ürün ekleme ekranını test et</a></p><p>Rollback: <code>/so_v391018683_supplier_popup_hard_fix_rollback.php?key=sesliofis391018683</code></p>',true);
