</main>
<script>
function toggleMenu(){
  document.getElementById('side').classList.toggle('open');
  document.getElementById('ov').classList.toggle('show');
}
</script>
<style>
.sp-toast{position:fixed;z-index:99999;display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:12px;color:#fff;font-size:15px;font-weight:600;line-height:1.3;background:linear-gradient(135deg,#16a34a,#15803d);box-shadow:0 10px 30px rgba(0,0,0,.25);opacity:0;transition:opacity .35s ease,transform .35s ease}
.sp-toast .ic{flex:none;width:26px;height:26px;border-radius:50%;background:#fff;color:#16a34a;display:flex;align-items:center;justify-content:center;font-weight:800}
.sp-toast .x{margin-left:auto;background:none;border:0;color:#fff;font-size:20px;line-height:1;cursor:pointer;padding:0 0 0 8px}
@media(min-width:768px){.sp-toast{top:24px;right:24px;min-width:280px;max-width:420px;transform:translateX(40px)}.sp-toast.show{opacity:1;transform:translateX(0)}}
@media(max-width:767px){.sp-toast{top:calc(12px + env(safe-area-inset-top,0px));left:12px;right:12px;font-size:14px;padding:12px 14px;transform:translateY(-30px)}.sp-toast.show{opacity:1;transform:translateY(0)}}
</style>
<script>
(function(){
  var m = new URLSearchParams(location.search).get('msg');
  if(!m) return;
  var map = {
    added:'Added successfully',
    deleted:'Deleted successfully',
    replaced:'Image replaced successfully',
    updated:'Updated successfully',
    saved:'Saved successfully'
  };
  var text = map[m] || ('Done: ' + m);

  var t = document.createElement('div');
  t.className = 'sp-toast';
  t.setAttribute('role','status');
  t.innerHTML = '<span class="ic">✓</span><span></span><button type="button" class="x" aria-label="Close">×</button>';
  t.children[1].textContent = text;
  document.body.appendChild(t);

  function hide(){ t.classList.remove('show'); setTimeout(function(){ t.remove(); }, 400); }
  t.querySelector('.x').addEventListener('click', hide);
  setTimeout(function(){ t.classList.add('show'); }, 50);
  setTimeout(hide, 3500);

  if(history.replaceState){
    var u = new URL(location.href);
    u.searchParams.delete('msg');
    history.replaceState(null, '', u.pathname + u.search);
  }
})();
</script>
<style>
.sp-prev{display:grid;grid-template-columns:repeat(auto-fill,minmax(96px,1fr));gap:10px;margin:12px 0}
.sp-prev .it{position:relative;border:1px solid #dfe5f2;border-radius:10px;overflow:hidden;background:#fff}
.sp-prev img{width:100%;height:90px;object-fit:cover;display:block}
.sp-prev button{position:absolute;top:4px;right:4px;width:24px;height:24px;border:0;border-radius:50%;background:rgba(220,38,38,.92);color:#fff;font-size:16px;line-height:1;cursor:pointer;padding:0}
</style>
<script>
(function(){
  if(!/product-(add|edit)\.php/.test(location.pathname)) return;
  var input=document.querySelector('input[type=file][name^="images"]');
  if(!input) return;
  var store=new DataTransfer();
  var box=document.createElement('div'); box.className='sp-prev';
  var anchor=input.closest('label')||input.parentElement;
  anchor.insertAdjacentElement('afterend',box);
  function key(f){return f.name+'|'+f.size+'|'+f.lastModified;}
  function render(){
    box.innerHTML='';
    Array.prototype.forEach.call(store.files,function(f,i){
      var it=document.createElement('div'); it.className='it';
      var im=document.createElement('img'); im.src=URL.createObjectURL(f); im.alt='';
      var b=document.createElement('button'); b.type='button'; b.textContent='×'; b.setAttribute('aria-label','Remove');
      b.addEventListener('click',function(){
        var n=new DataTransfer();
        Array.prototype.forEach.call(store.files,function(x,j){ if(j!==i) n.items.add(x); });
        store=n; input.files=store.files; render();
      });
      it.appendChild(im); it.appendChild(b); box.appendChild(it);
    });
  }
  input.addEventListener('change',function(){
    var have={};
    Array.prototype.forEach.call(store.files,function(f){have[key(f)]=1;});
    Array.prototype.forEach.call(input.files,function(f){ if(!have[key(f)]) store.items.add(f); });
    input.files=store.files; render();
  });
})();
</script>
</body>
</html>