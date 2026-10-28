(function(){
  const launch=document.getElementById('shivaChatLauncher'), panel=document.getElementById('shivaChatPanel'), close=document.getElementById('shivaChatClose'), body=document.getElementById('shivaChatBody'), form=document.getElementById('shivaChatForm'), input=document.getElementById('shivaChatInput');
  if(!launch||!panel||!body) return;

  const DATA={
    products:{title:'Our Products',text:'We provide precision tooling and die solutions for printing, packaging and converting applications.',link:'our-product.html',label:'View 19 Products'},
    office:{title:'Malad Unit',text:'Malad West, Mumbai — S NO 223 CTS-2675 HISSA-687, Kharodi Marve Road, Malwani, Malad West, Nr Chheda Complex, Mumbai 400095.',link:'https://www.google.com/maps/search/?api=1&query=S+NO+223+CTS-2675+HISSA-687%2C+Kharodi+Marve+Road%2C+Malwani%2C+Malad+West%2C+Nr+Chheda+Complex%2C+Mumbai+400095',label:'Open Malad Unit Location'},
    factory:{title:'Vasai Unit',text:'Gala No: C-2, Shree Ganesh Ind Estate, Behind Sagar Hotel, Chinchoti, Devdal, Kaman, Vasai (East) – 401208.',link:'https://www.google.com/maps/search/?api=1&query=Gala+No+C-2+Shree+Ganesh+Ind+Estate+Behind+Sagar+Hotel+Chinchoti+Devdal+Kaman+Vasai+East+401208',label:'Open Vasai Unit Location'},
    contact:{title:'Contact Team',text:'Our team is available on these numbers: +91 88282 30576, +91 72089 30779, +91 88282 30575, +91 84465 42824. For email enquiries use shivapunchart@gmail.com.',link:'mailto:shivapunchart@gmail.com',label:'Send Email'},
    whatsapp:{title:'WhatsApp',text:'Start a direct WhatsApp conversation with our team.',link:'https://wa.me/918433538141',label:'Open WhatsApp'}
  };

  const productNames=['3D Emboss','Laser Dies','Pre Rubber Dies','Punch + Emboss Online','Label Dies','Sandwich Dies','Stripping / Blanking Dies','Creasing Plates','Foil Blocks','Emboss Dies','Micro Effect','Step Emboss','Braille Emboss','Rotary Dies','Flexible Dies','Hot Foil Dies','Cutting Dies','Counter Plates','Special Purpose Dies'];
  let greeted=false;

  function add(text,who='bot'){
    const el=document.createElement('div'); el.className='shiva-bubble '+who; el.textContent=text; body.appendChild(el); body.scrollTop=body.scrollHeight; return el;
  }
  function typing(){
    const el=document.createElement('div'); el.className='shiva-bubble bot shiva-typing'; el.innerHTML='<span></span><span></span><span></span>'; body.appendChild(el); body.scrollTop=body.scrollHeight; return el;
  }
  function setOpen(open){
    panel.classList.toggle('open',!!open); panel.setAttribute('aria-hidden',String(!open)); launch.setAttribute('aria-expanded',String(!!open));
    if(open && !greeted){ greeted=true; setTimeout(()=>add('I can help with products, quotations, locations, contact details and WhatsApp.'),220); }
    if(open&&input) setTimeout(()=>input.focus(),80);
  }
  function addLink(item){
    if(!item||!item.link)return;
    const wrap=document.createElement('div'); wrap.className='shiva-chat-link-row';
    const a=document.createElement('a'); a.href=item.link; a.target=item.link.indexOf('mailto:')===0?'_self':'_blank'; a.rel='noopener'; a.textContent=item.label; wrap.appendChild(a); body.appendChild(wrap); body.scrollTop=body.scrollHeight;
  }
  function show(key){
    const item=DATA[key]; if(!item)return;
    add(item.title,'user'); const t=typing(); setTimeout(()=>{t.remove();add(item.text);addLink(item);},320);
  }
  function showProducts(){
    add('Our Products','user'); const t=typing(); setTimeout(()=>{
      t.remove(); add('Here are our 19 product categories:');
      const box=document.createElement('div'); box.className='shiva-product-chat-list';
      productNames.forEach((name,i)=>{const row=document.createElement('a');row.href='our-product.html';row.textContent=(i+1)+'. '+name;row.target='_self';box.appendChild(row);});
      body.appendChild(box); body.scrollTop=body.scrollHeight;
    },300);
  }
  function quote(){
    add('Get a Quote','user');
    const wrap=document.createElement('div'); wrap.className='shiva-quote-box';
    wrap.innerHTML='<input id="spqName" placeholder="Your name" /><input id="spqPhone" placeholder="Phone number" inputmode="tel" /><input id="spqEmail" placeholder="Email address" type="email" /><textarea id="spqReq" placeholder="Tell us your requirement"></textarea><button type="button" id="spqSend">Send Enquiry</button>';
    body.appendChild(wrap); body.scrollTop=body.scrollHeight;
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

  launch.addEventListener('click',e=>{e.stopPropagation();setOpen(!panel.classList.contains('open'));});
  close&&close.addEventListener('click',e=>{e.preventDefault();e.stopPropagation();setOpen(false);});
  document.addEventListener('click',e=>{
    const btn=e.target.closest('[data-chat]');
    if(btn){e.preventDefault(); const key=btn.dataset.chat; if(key==='products')showProducts(); else if(key==='quote')quote(); else show(key); return;}
    if(panel.classList.contains('open')&&!panel.contains(e.target)&&!launch.contains(e.target))setOpen(false);
  });
  form&&form.addEventListener('submit',e=>{e.preventDefault();const text=(input.value||'').trim();if(!text)return;add(text,'user');input.value='';const t=typing();setTimeout(()=>{t.remove();add(answer(text));if(/quote|quotation|enquiry|inquiry|requirement|price/.test(text.toLowerCase())){setTimeout(quote,180);}else if(/product|die|emboss|punch|laser/.test(text.toLowerCase())){addLink(DATA.products);}},380);});
  document.addEventListener('keydown',e=>{if(e.key==='Escape')setOpen(false);});
})();
