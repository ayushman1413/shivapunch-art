(function(){
  const launch=document.getElementById('shivaChatLauncher'), panel=document.getElementById('shivaChatPanel'), close=document.getElementById('shivaChatClose'), body=document.getElementById('shivaChatBody'), form=document.getElementById('shivaChatForm'), input=document.getElementById('shivaChatInput');
  if(!launch||!panel||!body) return;

  const BOT_NAME='Shiva Punch Assistant';
  const logoImg=launch.querySelector('img');
  const LOGO=logoImg?logoImg.getAttribute('src'):'';
  const firstStatic=body.querySelector('.shiva-bubble');
  const GREET=firstStatic?firstStatic.textContent.trim():'Hi! How can I help you today?';
  const GREET2='I can help with products, quotations, locations, contact details and WhatsApp.';
  body.innerHTML='';

  const st=document.createElement('style');
  st.textContent=''
    +'.shiva-row{display:flex;align-items:flex-end;gap:8px;margin:8px 0;max-width:100%}'
    +'.shiva-row.user{justify-content:flex-end}'
    +'.shiva-avatar{width:28px;height:28px;border-radius:50%;object-fit:cover;flex:0 0 28px;background:#fff}'
    +'.shiva-col{display:flex;flex-direction:column;min-width:0;max-width:85%}'
    +'.shiva-row.user .shiva-col{align-items:flex-end}'
    +'.shiva-col .shiva-bubble{margin:0!important;max-width:100%!important;align-self:flex-start!important}'
    +'.shiva-row.user .shiva-col .shiva-bubble{align-self:flex-end!important}'
    +'.shiva-meta{font-size:10px;color:rgba(226,232,240,.65);margin-top:3px;padding:0 4px}'
    +'.shiva-day{text-align:center;font-size:11px;color:rgba(226,232,240,.65);margin:8px 0}'
    +'.shiva-head-actions{position:relative;display:inline-flex;align-items:center;gap:2px}'
    +'.shiva-menu-btn{background:none;border:0;color:#fff;font-size:22px;line-height:1;cursor:pointer;padding:4px 8px}'
    +'.shiva-menu{position:absolute;top:100%;right:0;background:#fff;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.25);min-width:120px;z-index:10;overflow:hidden}'
    +'.shiva-menu[hidden]{display:none}'
    +'.shiva-menu button{display:block;width:100%;background:none;border:0;padding:10px 14px;font-size:13px;color:#0f172a;text-align:left;cursor:pointer}';
  document.head.appendChild(st);
  const st2=document.createElement('style');
st2.textContent='.shiva-product-chat-list{display:flex!important;flex-direction:row!important;flex-wrap:nowrap!important;width:100%!important;max-width:100%!important;overflow-x:auto!important;overflow-y:hidden!important;gap:8px!important;padding:4px 2px 10px!important;-webkit-overflow-scrolling:touch}.shiva-product-chat-list a{flex:0 0 auto!important;width:auto!important;white-space:nowrap!important;padding:8px 14px!important}';
document.head.appendChild(st2);
const st3=document.createElement('style');
st3.textContent='div:has(>button[data-chat]){display:flex!important;flex-wrap:nowrap!important;overflow-x:auto!important;overflow-y:hidden!important;gap:8px!important;scrollbar-width:none;-ms-overflow-style:none}div:has(>button[data-chat])::-webkit-scrollbar{display:none}div:has(>button[data-chat])>button[data-chat]{flex:0 0 auto!important;width:auto!important;white-space:nowrap!important;padding:8px 14px!important}.shiva-product-chat-list{scrollbar-width:none}.shiva-product-chat-list::-webkit-scrollbar{display:none}';
document.head.appendChild(st3);

  const DATA={
    products:{title:'Our Products',text:'We provide precision tooling and die solutions for printing, packaging and converting applications.',link:'our-product.html',label:'View 19 Products'},
    office:{title:'Malad Unit',text:'Malad West, Mumbai — S NO 223 CTS-2675 HISSA-687, Kharodi Marve Road, Malwani, Malad West, Nr Chheda Complex, Mumbai 400095.',link:'https://www.google.com/maps/search/?api=1&query=S+NO+223+CTS-2675+HISSA-687%2C+Kharodi+Marve+Road%2C+Malwani%2C+Malad+West%2C+Nr+Chheda+Complex%2C+Mumbai+400095',label:'Open Malad Unit Location'},
    factory:{title:'Vasai Unit',text:'Gala No: C-2, Shree Ganesh Ind Estate, Behind Sagar Hotel, Chinchoti, Devdal, Kaman, Vasai (East) – 401208.',link:'https://www.google.com/maps/search/?api=1&query=Gala+No+C-2+Shree+Ganesh+Ind+Estate+Behind+Sagar+Hotel+Chinchoti+Devdal+Kaman+Vasai+East+401208',label:'Open Vasai Unit Location'},
    contact:{title:'Contact Team',text:'Our team is available on these numbers: +91 88282 30576, +91 72089 30779, +91 88282 30575, +91 84465 42824. For email enquiries use shivapunchart@gmail.com.',link:'mailto:shivapunchart@gmail.com',label:'Send Email'},
    whatsapp:{title:'WhatsApp',text:'Start a direct WhatsApp conversation with our team.',link:'https://wa.me/918433538141',label:'Open WhatsApp'}
  };

  const productNames=['3D Emboss','Laser Dies','Pre Rubber Dies','Punch + Emboss Online','Label Dies','Sandwich Dies','Stripping / Blanking Dies','Creasing Plates','Foil Blocks','Emboss Dies','Micro Effect','Step Emboss','Braille Emboss','Rotary Dies','Flexible Dies','Hot Foil Dies','Cutting Dies','Counter Plates','Special Purpose Dies'];
  let greeted=false;
  let menuEl=null;

  function nowTime(){ return new Date().toLocaleTimeString([], {hour:'numeric',minute:'2-digit'}); }
  function delay(){ return 1000+Math.random()*1000; }
  function scrollDown(){ body.scrollTop=body.scrollHeight; }

  function makeRow(who){
    const el=document.createElement('div'); el.className='shiva-row '+who;
    if(who==='bot'&&LOGO){ const av=document.createElement('img'); av.className='shiva-avatar'; av.src=LOGO; av.alt=''; el.appendChild(av); }
    const col=document.createElement('div'); col.className='shiva-col'; el.appendChild(col);
    return {el:el,col:col};
  }
  function add(text,who='bot'){
    const r=makeRow(who);
    const b=document.createElement('div'); b.className='shiva-bubble '+who; b.textContent=text; r.col.appendChild(b);
    const m=document.createElement('div'); m.className='shiva-meta'; m.textContent=(who==='bot'?BOT_NAME+' • ':'')+nowTime(); r.col.appendChild(m);
    body.appendChild(r.el); scrollDown(); return b;
  }
  function typing(){
    const r=makeRow('bot');
    const b=document.createElement('div'); b.className='shiva-bubble bot shiva-typing'; b.innerHTML='<span></span><span></span><span></span>'; r.col.appendChild(b);
    body.appendChild(r.el); scrollDown(); return r.el;
  }
  function botSay(text,after){
    const t=typing();
    setTimeout(()=>{t.remove();add(text);if(after)after();},delay());
  }
  function addDay(){
    const d=document.createElement('div'); d.className='shiva-day'; d.textContent='Today • '+nowTime(); body.appendChild(d);
  }
  function setOpen(open){
    panel.classList.toggle('open',!!open); panel.setAttribute('aria-hidden',String(!open)); launch.setAttribute('aria-expanded',String(!!open));
    if(open && !greeted){ greeted=true; addDay(); botSay(GREET,()=>botSay(GREET2)); }
    if(open&&input) setTimeout(()=>input.focus(),80);
  }
  function addLink(item){
    if(!item||!item.link)return;
    const wrap=document.createElement('div'); wrap.className='shiva-chat-link-row';
    const a=document.createElement('a'); a.href=item.link; a.target=item.link.indexOf('mailto:')===0?'_self':'_blank'; a.rel='noopener'; a.textContent=item.label; wrap.appendChild(a); body.appendChild(wrap); scrollDown();
  }
  function show(key){
    const item=DATA[key]; if(!item)return;
    add(item.title,'user'); const t=typing(); setTimeout(()=>{t.remove();add(item.text);addLink(item);},delay());
  }
  function showProducts(){
    add('Our Products','user'); const t=typing(); setTimeout(()=>{
      t.remove(); add('Here are our 19 product categories:');
      const box=document.createElement('div'); box.className='shiva-product-chat-list';
      productNames.forEach((name,i)=>{const row=document.createElement('a');row.href='our-product.html';row.textContent=(i+1)+'. '+name;row.target='_self';box.appendChild(row);});
      body.appendChild(box); scrollDown();
    },delay());
  }
  function quote(){
    add('Get a Quote','user');
    const wrap=document.createElement('div'); wrap.className='shiva-quote-box';
    wrap.innerHTML='<input id="spqName" placeholder="Your name" /><input id="spqPhone" placeholder="Phone number" inputmode="tel" /><input id="spqEmail" placeholder="Email address" type="email" /><textarea id="spqReq" placeholder="Tell us your requirement"></textarea><button type="button" id="spqSend">Send Enquiry</button>';
    body.appendChild(wrap); scrollDown();
    wrap.querySelector('#spqSend').addEventListener('click',function(){
      const n=wrap.querySelector('#spqName').value.trim(),p=wrap.querySelector('#spqPhone').value.trim(),e=wrap.querySelector('#spqEmail').value.trim(),r=wrap.querySelector('#spqReq').value.trim();
      if(!n||!p||!r){add('Please enter your name, phone number and requirement.');return;}
      const subject=encodeURIComponent('Website Quote Enquiry - '+n); const msg=encodeURIComponent('Name: '+n+'\nPhone: '+p+'\nEmail: '+e+'\nRequirement: '+r);
      add('Your enquiry details are ready. Opening email…'); setTimeout(()=>{window.location.href='mailto:shivapunchart@gmail.com?subject='+subject+'&body='+msg;},250);
    });
  }
  function answer(text){
    const q=text.toLowerCase();
    if(/quote|quotation|price|enquiry|inquiry|requirement/.test(q)) return 'Sure — I can help you prepare a quotation enquiry.';
    if(/product|products|die|dies|emboss|punch|laser|rubber|label|sandwich|foil|braille|creasing/.test(q)) return DATA.products.text;
    if(/office|head office|malad|mumbai/.test(q)) return DATA.office.text;
    if(/factory|vasai|chinchoti|kaman/.test(q)) return DATA.factory.text;
    if(/whatsapp|chat/.test(q)) return DATA.whatsapp.text;
    if(/contact|phone|call|email|mail/.test(q)) return DATA.contact.text;
    if(/hello|hi|hey|namaste/.test(q)) return 'Hi! 👋 How can I help you today? You can ask about products, quotation, Malad Unit, Vasai Unit, contact or WhatsApp.';
    return 'I can help with Products, Get a Quote, Malad Unit, Vasai Unit, Contact and WhatsApp. Choose an option below or type your question.';
  }

  if(close&&close.parentNode){
    const head=close.parentNode;
    const wrap=document.createElement('span'); wrap.className='shiva-head-actions';
    const dots=document.createElement('button'); dots.type='button'; dots.className='shiva-menu-btn'; dots.setAttribute('aria-label','Chat menu'); dots.textContent='⋮';
    const menu=document.createElement('div'); menu.className='shiva-menu'; menu.hidden=true; menuEl=menu;
    const end=document.createElement('button'); end.type='button'; end.textContent='End chat'; menu.appendChild(end);
    head.insertBefore(wrap,close); wrap.appendChild(dots); wrap.appendChild(close); wrap.appendChild(menu);
    dots.addEventListener('click',e=>{e.preventDefault();e.stopPropagation();menu.hidden=!menu.hidden;});
    end.addEventListener('click',e=>{
      e.preventDefault();e.stopPropagation();menu.hidden=true;
      if(confirm('End this chat?')){ body.innerHTML=''; greeted=false; setOpen(false); }
    });
  }

  launch.addEventListener('click',e=>{e.stopPropagation();setOpen(!panel.classList.contains('open'));});
  close&&close.addEventListener('click',e=>{e.preventDefault();e.stopPropagation();setOpen(false);});
  document.addEventListener('click',e=>{
    if(menuEl) menuEl.hidden=true;
    const btn=e.target.closest('[data-chat]');
    if(btn){e.preventDefault(); const key=btn.dataset.chat; if(key==='products')showProducts(); else if(key==='quote')quote(); else show(key); return;}
    if(panel.classList.contains('open')&&!panel.contains(e.target)&&!launch.contains(e.target))setOpen(false);
  });
  form&&form.addEventListener('submit',e=>{e.preventDefault();const text=(input.value||'').trim();if(!text)return;add(text,'user');input.value='';const t=typing();setTimeout(()=>{t.remove();add(answer(text));if(/quote|quotation|enquiry|inquiry|requirement|price/.test(text.toLowerCase())){setTimeout(quote,180);}else if(/product|die|emboss|punch|laser/.test(text.toLowerCase())){addLink(DATA.products);}},delay());});
  document.addEventListener('keydown',e=>{if(e.key==='Escape')setOpen(false);});
  // ---- Chat save/restore: refresh par chat wahi rahe ----
  const KEY='shivaChatState_v1';
  function saveState(){
    try{
      const tmp=document.createElement('div'); tmp.innerHTML=body.innerHTML;
      tmp.querySelectorAll('.shiva-typing,.shiva-quote-box').forEach(n=>{const r=n.closest('.shiva-row'); (r||n).remove();});
      const html=tmp.innerHTML.trim();
      if(!html){localStorage.removeItem(KEY);return;}
      localStorage.setItem(KEY,JSON.stringify({html:html,open:panel.classList.contains('open'),t:Date.now()}));
    }catch(e){}
  }
  try{
    const saved=JSON.parse(localStorage.getItem(KEY)||'null');
    if(saved&&saved.html&&Date.now()-saved.t<86400000){
      body.innerHTML=saved.html; greeted=true;
      if(saved.open){ panel.classList.add('open'); panel.setAttribute('aria-hidden','false'); launch.setAttribute('aria-expanded','true'); }
      setTimeout(scrollDown,100);
    } else { localStorage.removeItem(KEY); }
  }catch(e){}
  new MutationObserver(saveState).observe(body,{childList:true,subtree:true,characterData:true});
  new MutationObserver(saveState).observe(panel,{attributes:true,attributeFilter:['class']});
  /* ===== Shiva Punch Art: smart replies ===== */
var RULES = [
 {k:/^(hi|hii+|hello|hey|good (morning|afternoon|evening))\b/, r:"Hello! 👋 Welcome to Shiva Punch Art. How can I help you today?"},
 {k:/how are you|how r u|what'?s up/, r:"I'm doing great, thank you! How can I help you with our products or services?"},
 {k:/^(yes|yeah|yep|yup|sure)\b/, r:"Great! Please tell me what you need: products, a quote, our locations or contact details."},
 {k:/^(no|nope|not now|nothing)\b/, r:"No problem! If you need anything later, I'm right here."},
 {k:/^(ok|okay|k|fine|alright|cool|got it|nice|good|great)\b/, r:"Great! Let me know if you need anything else."},
 {k:/thank|thanks|thx/, r:"You're welcome! 😊 Happy to help."},
 {k:/\b(bye|goodbye|see you|take care)\b/, r:"Thank you for visiting Shiva Punch Art. Have a great day! 👋"},
 {k:/\b(time|timing|timings|hours|open|opening|close|closing|closed|working|holiday|sunday|saturday)\b/, r:"We are open Monday to Saturday, 9:00 AM to 8:00 PM. Sunday is closed. You can call us on +91 72089 30779."}, 
 {k:/who are you|your name|are you (a )?(bot|human)/, r:"I'm Shiva Punch Assistant, the virtual assistant of Shiva Punch Art."},
 {k:/\b(price|rate|cost|quote|quotation|estimate)\b/, r:"Pricing depends on size, material and design. Tap 'Get a Quote' below and our team will get back to you."},
 {k:/\b(product|products|service|services|die|dies|emboss|foil|laser|creasing|braille|punch)\b/, r:"We offer 19 types of punch, die and embossing solutions. Tap 'Products' below to see the full list."},
 {k:/malad/, r:"Tap 'Malad Unit' below to see our Malad address."},
 {k:/vasai|factory|unit/, r:"Tap 'Vasai Unit' below to see our factory address."},
 {k:/\b(address|location|where|office|visit)\b/, r:"We have a Malad Unit and a Vasai Unit. Tap either button below for the address."},
 {k:/\b(phone|call|number|contact|email|mail)\b/, r:"You can call us at +91 72089 30779 or email shivapunchart@gmail.com."},
 {k:/whatsapp/, r:"Tap the 'WhatsApp' button below to chat with our team directly."},
 {k:/\b(about|company|since|experience)\b/, r:"Shiva Punch Art is a printing and packaging industry die maker, offering precision punch, die and embossing solutions."}
];
var FALLBACK = "I can help with our products, quotations, locations, contact details and WhatsApp. Please choose an option below or call +91 72089 30779.";

function smartReply(text){
  var t = text.toLowerCase().trim();
  for (var i = 0; i < RULES.length; i++){
    if (RULES[i].k.test(t)) return RULES[i].r;
  }
  return FALLBACK;
}

var chatForm  = document.getElementById('shivaChatForm');
var chatInput = document.getElementById('shivaChatInput');
if (chatForm && chatInput){
  chatForm.addEventListener('submit', function(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    var text = chatInput.value.trim();
    if (!text) return;
    add(text, 'user');
    chatInput.value = '';
    var t = typing();
    setTimeout(function(){
      t.remove();
      add(smartReply(text));
      scrollDown();
    }, 800);
  }, true);
}
})();
