<?php
/** SesliOfis v3.9.10.186.8.2 — Supplier Popup Fixed Header / Mobile Overflow Fix */
const SO_V18682_KEY = 'sesliofis391018682';
const SO_V18682_MARKER_CSS = 'SO_SUPPLIER_POPUP_V391018682_CSS_START';
const SO_V18682_MARKER_JS = 'SO_SUPPLIER_POPUP_V391018682_JS_START';
const SO_V18682_PRODUCT_WRITE_SHA = 'c919d0300733a7b0887eca96c19fdf5e08a81aabe00c7919c863e5de1e5d510e';

function so18682_h($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function so18682_page($title,$body,$ok=false){
    $color=$ok?'#167347':'#b42318';
    echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.so18682_h($title).'</title></head><body style="font-family:Arial,sans-serif;background:#f4f7fa;padding:24px;color:#10233f"><main style="max-width:760px;margin:auto;background:#fff;border:1px solid #dce5ee;border-radius:16px;padding:24px"><h1 style="color:'.$color.'">'.so18682_h($title).'</h1>'.$body.'</main></body></html>';
    exit;
}
function so18682_fail($m){so18682_page('Kurulum durduruldu','<p>'.so18682_h($m).'</p>',false);}
function so18682_atomic($path,$content){
    $tmp=$path.'.tmp.'.bin2hex(random_bytes(5));
    if(file_put_contents($tmp,$content,LOCK_EX)===false){throw new RuntimeException('Geçici dosya yazılamadı: '.$path);}
    @chmod($tmp,0644);
    if(!@rename($tmp,$path)){@unlink($tmp);throw new RuntimeException('Dosya atomik değiştirilemedi: '.$path);}
}
function so18682_manifest_write($path,array $data){
    $json=json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    if($json===false){throw new RuntimeException('Manifest oluşturulamadı.');}
    so18682_atomic($path,$json);
}

$key=isset($_GET['key'])?(string)$_GET['key']:'';
if(!hash_equals(SO_V18682_KEY,$key)){so18682_fail('Geçersiz installer anahtarı.');}
$root=realpath(__DIR__);
if($root===false||basename($root)!=='app.sesliofis.com'){so18682_fail('Dosya doğrudan app.sesliofis.com kökünde olmalıdır.');}
$productWrite=$root.'/product_write.php';
$cssFile=$root.'/product_types_v39101865.css';
$jsFile=$root.'/product_types_v39101865.js';
$manifestFile=$root.'/_rollback_v391018682_supplier_popup.json';
foreach(array($productWrite,$cssFile,$jsFile) as $required){if(!is_file($required)){so18682_fail('Gerekli canlı dosya bulunamadı: '.basename($required));}}
$liveSha=hash_file('sha256',$productWrite);
if(!hash_equals(SO_V18682_PRODUCT_WRITE_SHA,$liveSha)){so18682_fail('Safe-point uyuşmuyor. product_write.php SHA-256: '.$liveSha);}
$css=file_get_contents($cssFile);$js=file_get_contents($jsFile);
if($css===false||$js===false){so18682_fail('Canlı CSS/JS okunamadı.');}
if(strpos($css,SO_V18682_MARKER_CSS)!==false&&strpos($js,SO_V18682_MARKER_JS)!==false){
    so18682_page('Zaten kurulu','<p>v3.9.10.186.8.2 popup düzeltmesi daha önce kurulmuş.</p><p><a href="/products/new">Ürün ekleme ekranını test et</a></p>',true);
}

$cssPatch=<<<'CSS'

/* SO_SUPPLIER_POPUP_V391018682_CSS_START */
html.so-supplier-popup-open-v391018682,
body.so-supplier-popup-open-v391018682{overflow:hidden!important;overscroll-behavior:none!important;max-width:100%!important}
.so-supplier-popup-overlay-v391018682{position:fixed!important;inset:0!important;width:100vw!important;height:100dvh!important;max-width:none!important;max-height:none!important;margin:0!important;padding:max(10px,env(safe-area-inset-top)) 10px max(10px,env(safe-area-inset-bottom))!important;display:flex!important;align-items:flex-start!important;justify-content:center!important;overflow-x:hidden!important;overflow-y:auto!important;overscroll-behavior:contain!important;background:rgba(4,18,38,.64)!important;-webkit-backdrop-filter:blur(2px);backdrop-filter:blur(2px);box-sizing:border-box!important;z-index:2147483000!important;transform:none!important;isolation:isolate!important}
.so-supplier-popup-panel-v391018682{position:relative!important;width:min(620px,calc(100vw - 20px))!important;max-width:620px!important;min-width:0!important;max-height:calc(100dvh - max(20px,env(safe-area-inset-top)) - max(20px,env(safe-area-inset-bottom)))!important;margin:0 auto!important;overflow-x:hidden!important;overflow-y:auto!important;overscroll-behavior:contain!important;box-sizing:border-box!important;transform:none!important;z-index:1!important}
.so-supplier-popup-panel-v391018682,.so-supplier-popup-panel-v391018682 *{box-sizing:border-box!important;min-width:0!important}
.so-supplier-popup-panel-v391018682 form,.so-supplier-popup-panel-v391018682 fieldset,.so-supplier-popup-panel-v391018682 input,.so-supplier-popup-panel-v391018682 select,.so-supplier-popup-panel-v391018682 textarea,.so-supplier-popup-panel-v391018682 button,.so-supplier-popup-panel-v391018682 img,.so-supplier-popup-panel-v391018682 table{max-width:100%!important}
.so-supplier-popup-panel-v391018682 input,.so-supplier-popup-panel-v391018682 select,.so-supplier-popup-panel-v391018682 textarea{width:100%!important}
.so-supplier-popup-panel-v391018682 h1,.so-supplier-popup-panel-v391018682 h2,.so-supplier-popup-panel-v391018682 h3,.so-supplier-popup-panel-v391018682 p,.so-supplier-popup-panel-v391018682 label,.so-supplier-popup-panel-v391018682 span,.so-supplier-popup-panel-v391018682 a,.so-supplier-popup-panel-v391018682 td,.so-supplier-popup-panel-v391018682 th,.so-supplier-popup-panel-v391018682 button{overflow-wrap:anywhere!important;word-break:break-word!important}
.so-supplier-popup-panel-v391018682 [class*="result"],.so-supplier-popup-panel-v391018682 [class*="list"],.so-supplier-popup-panel-v391018682 [id*="result"],.so-supplier-popup-panel-v391018682 [id*="list"]{max-height:min(42dvh,360px)!important;overflow-x:hidden!important;overflow-y:auto!important;overscroll-behavior:contain!important}
@media(max-width:430px){.so-supplier-popup-overlay-v391018682{padding-left:8px!important;padding-right:8px!important}.so-supplier-popup-panel-v391018682{width:calc(100vw - 16px)!important;max-width:calc(100vw - 16px)!important;border-radius:14px!important}.so-supplier-popup-panel-v391018682 [class*="actions"],.so-supplier-popup-panel-v391018682 [class*="buttons"]{flex-wrap:wrap!important}}
/* SO_SUPPLIER_POPUP_V391018682_CSS_END */
CSS;

$jsPatch=<<<'JS'

/* SO_SUPPLIER_POPUP_V391018682_JS_START */
(function(){'use strict';
var OPEN='so-supplier-popup-open-v391018682',OVERLAY='so-supplier-popup-overlay-v391018682',PANEL='so-supplier-popup-panel-v391018682';
var scrollY=0,locked=false,active=[];
function low(v){return String(v||'').toLocaleLowerCase('tr-TR')}
function visible(el){if(!el||!el.isConnected||el.hidden||el.getAttribute('aria-hidden')==='true')return false;var s=getComputedStyle(el);if(s.display==='none'||s.visibility==='hidden'||Number(s.opacity)===0)return false;var r=el.getBoundingClientRect();return r.width>0&&r.height>0}
function supplierText(el){var id=el.id||'',cl=typeof el.className==='string'?el.className:'',tx=(el.textContent||'').slice(0,900);var x=low(id+' '+cl+' '+tx);return x.indexOf('tedarik')!==-1||x.indexOf('supplier')!==-1}
function modalLike(el){if(!el||!el.matches)return false;var x=low((el.id||'')+' '+(typeof el.className==='string'?el.className:''));if(el.matches('dialog,[role="dialog"],[aria-modal="true"]'))return true;if(/modal|dialog|popup|overlay|backdrop/.test(x))return true;var s=getComputedStyle(el),r=el.getBoundingClientRect();return s.position==='fixed'&&r.width>window.innerWidth*.45&&r.height>window.innerHeight*.25}
function findRoot(node){var n=node,steps=0,best=null;while(n&&n!==document.body&&steps<9){if(modalLike(n))best=n;n=n.parentElement;steps++}return best}
function panelOf(root){if(root.matches('dialog,[role="dialog"],.modal-dialog,.modal-content'))return root;return root.querySelector('.modal-dialog,.modal-content,[role="dialog"],dialog,[class*="panel"],[class*="content"],form')||root.firstElementChild||root}
function lock(){if(locked)return;locked=true;scrollY=window.scrollY||0;document.documentElement.classList.add(OPEN);document.body.classList.add(OPEN);document.body.dataset.soSupplierOldStyle=document.body.getAttribute('style')||'';document.body.style.position='fixed';document.body.style.top=(-scrollY)+'px';document.body.style.left='0';document.body.style.right='0';document.body.style.width='100%';document.body.style.overflow='hidden'}
function unlock(){if(!locked)return;locked=false;document.documentElement.classList.remove(OPEN);document.body.classList.remove(OPEN);var old=document.body.dataset.soSupplierOldStyle||'';if(old)document.body.setAttribute('style',old);else document.body.removeAttribute('style');delete document.body.dataset.soSupplierOldStyle;window.scrollTo(0,scrollY)}
function enhance(root){if(!root||!visible(root)||!supplierText(root)||root.dataset.soSupplierEnhanced==='1')return false;var panel=panelOf(root),overlay=root;if(panel===root||!((root.getBoundingClientRect().width>window.innerWidth*.7)&&(root.getBoundingClientRect().height>window.innerHeight*.6))){overlay=document.createElement('div');overlay.className=OVERLAY;var ph=document.createComment('so-supplier-popup-placeholder');root.parentNode.insertBefore(ph,root);overlay.__soPlaceholder=ph;overlay.__soOriginal=root;document.body.appendChild(overlay);overlay.appendChild(root)}else{root.classList.add(OVERLAY);if(root.parentElement!==document.body)document.body.appendChild(root)}
root.dataset.soSupplierEnhanced='1';panel.classList.add(PANEL);overlay.dataset.soSupplierOverlay='1';active.push({overlay:overlay,root:root,panel:panel});lock();return true}
function cleanup(){var next=[];for(var i=0;i<active.length;i++){var a=active[i];if(a.root&&visible(a.root)){next.push(a);continue}if(a.panel)a.panel.classList.remove(PANEL);if(a.root){a.root.classList.remove(OVERLAY);delete a.root.dataset.soSupplierEnhanced}if(a.overlay&&a.overlay!==a.root){var ph=a.overlay.__soPlaceholder;if(ph&&ph.parentNode&&a.root)ph.parentNode.insertBefore(a.root,ph);if(ph&&ph.parentNode)ph.parentNode.removeChild(ph);if(a.overlay.parentNode)a.overlay.parentNode.removeChild(a.overlay)}}active=next;if(!active.length)unlock()}
function scan(base){var scope=base&&base.querySelectorAll?base:document,nodes=[];if(scope.nodeType===1)nodes.push(scope);var q=scope.querySelectorAll('[id],[class],[role="dialog"],[aria-modal="true"],dialog');for(var i=0;i<q.length;i++)nodes.push(q[i]);for(var j=0;j<nodes.length;j++){if(!visible(nodes[j])||!supplierText(nodes[j]))continue;var r=findRoot(nodes[j]);if(r)enhance(r)}cleanup()}
function schedule(){setTimeout(function(){scan(document)},0);setTimeout(function(){scan(document)},70);setTimeout(function(){scan(document)},180);setTimeout(function(){scan(document)},420)}
document.addEventListener('click',function(e){var t=e.target&&e.target.closest?e.target.closest('button,a,[role="button"],label'):null,x=low(t?(t.textContent||t.getAttribute('aria-label')||''):'');if(x.indexOf('tedarik')!==-1||x.indexOf('supplier')!==-1||x.indexOf('yeni ekle')!==-1)schedule();else if(x.indexOf('kapat')!==-1||x.indexOf('vazgeç')!==-1||x==='x'||x==='×')setTimeout(cleanup,120)},true);
document.addEventListener('keydown',function(e){if(e.key==='Escape')setTimeout(cleanup,100)});
new MutationObserver(function(m){for(var i=0;i<m.length;i++)for(var j=0;j<m[i].addedNodes.length;j++)if(m[i].addedNodes[j].nodeType===1)scan(m[i].addedNodes[j]);cleanup()}).observe(document.documentElement,{childList:true,subtree:true,attributes:true,attributeFilter:['class','style','hidden','aria-hidden']});
document.addEventListener('DOMContentLoaded',function(){scan(document)});window.addEventListener('pageshow',function(){scan(document)});
}());
/* SO_SUPPLIER_POPUP_V391018682_JS_END */
JS;

$dataRoot=dirname($root);
$backupRoot=$dataRoot.'/_app_backups';
if(!is_dir($backupRoot)&&!mkdir($backupRoot,0750,true)&&!is_dir($backupRoot)){so18682_fail('Yedek klasörü oluşturulamadı.');}
$backupDir=$backupRoot.'/supplier_popup_v391018682_'.date('Ymd_His').'_'.substr(bin2hex(random_bytes(5)),0,10);
if(!mkdir($backupDir,0750,true)){so18682_fail('Sürüm yedek klasörü oluşturulamadı.');}
$backupCss=$backupDir.'/'.basename($cssFile);$backupJs=$backupDir.'/'.basename($jsFile);
if(!copy($cssFile,$backupCss)||!copy($jsFile,$backupJs)){so18682_fail('Canlı CSS/JS yedeklenemedi.');}
$manifest=array('version'=>'v3.9.10.186.8.2','created_at'=>date('c'),'backup_dir'=>$backupDir,'files'=>array(array('live'=>$cssFile,'backup'=>$backupCss,'sha256'=>hash_file('sha256',$cssFile)),array('live'=>$jsFile,'backup'=>$backupJs,'sha256'=>hash_file('sha256',$jsFile))));
try{
    so18682_atomic($cssFile,rtrim($css).$cssPatch."\n");
    so18682_atomic($jsFile,rtrim($js).$jsPatch."\n");
    if(strpos(file_get_contents($cssFile),SO_V18682_MARKER_CSS)===false||strpos(file_get_contents($jsFile),SO_V18682_MARKER_JS)===false){throw new RuntimeException('Kurulum doğrulaması başarısız.');}
    so18682_manifest_write($manifestFile,$manifest);
}catch(Throwable $e){@copy($backupCss,$cssFile);@copy($backupJs,$jsFile);so18682_fail('Kurulum geri alındı: '.$e->getMessage());}
so18682_page('Kurulum tamamlandı','<p>Tedarikçi popup katmanı fixed header üzerinde ve mobil taşma korumalı olarak kuruldu.</p><p><a href="/products/new">Ürün ekleme ekranını test et</a></p><p>Rollback: <code>https://app.sesliofis.com/so_v391018682_supplier_popup_fixed_header_rollback.php?key=sesliofis391018682</code></p>',true);
