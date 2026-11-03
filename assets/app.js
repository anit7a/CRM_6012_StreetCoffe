document.addEventListener('DOMContentLoaded',function(){
 const toggle=document.getElementById('menuToggle'),sidebar=document.getElementById('sidebar');
 if(toggle&&sidebar)toggle.addEventListener('click',()=>sidebar.classList.toggle('open'));
 const amount=document.getElementById('totalAmount'),preview=document.getElementById('pointsPreview');
 function updatePoints(){if(amount&&preview){const n=Math.max(0,Math.floor((parseInt(amount.value,10)||0)/10000));preview.textContent=n+' poin';}}
 if(amount){amount.addEventListener('input',updatePoints);updatePoints();}
});
