
let i=0;
let slides=document.querySelectorAll('.slide');

setInterval(()=>{
slides.forEach(s=>s.style.display='none');
slides[i].style.display='block';
i=(i+1)%slides.length;
},2500);
function openLogin(){
document.getElementById('loginModal').style.display='flex';
}
function closeLogin(){
document.getElementById('loginModal').style.display='none';
}
