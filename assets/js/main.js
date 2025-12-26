let timer;

function liveData(){
 clearInterval(timer);
 openPanel("Live Vitals");

 timer = setInterval(()=>{
  document.getElementById("data").innerHTML =
  `Temp: ${(36+Math.random()).toFixed(1)} °C<br>
   Oxygen: ${(95+Math.random()*4).toFixed(0)}%<br>
   BP: ${110+Math.floor(Math.random()*10)}/80`;
 },1500);
}

function heartRate(){
 clearInterval(timer);
 openPanel("Heart Rate");

 timer = setInterval(()=>{
   let bpm = 60 + Math.floor(Math.random()*40);
   document.getElementById("data").innerHTML = "BPM: <b>"+bpm+"</b>";
 },1000);
}

function openPanel(title){
 document.getElementById("panel").style.display="block";
 document.getElementById("title").innerText = title;
}

function closePanel(){
 clearInterval(timer);
 document.getElementById("panel").style.display="none";
}
