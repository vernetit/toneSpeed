<!DOCTYPE html>
<html>
<head>
  <title>toneSpeed</title>
  <script src="/js/jquery.min.js"></script>
  <!--<script src="/js/buzz.min.js"></script>-->
  <script src="/js/underscore-min.js"></script>
  <script src="/js/Tone.min.js"></script>
</head>
<body>
<input type="button" value="start" onclick="start(0)" id="start-btn">
v <input type="number" value="250" id="myVelocity" style="width: 40px;">
limit <input type="number" value="100" id="limit" style="width: 40px;">
<select id="mode" style="width: 90px;" onchange="">   
  <option value="0" selected>linear mono</option>
  <option value="1">triangle</option>
  <option value="2">cross</option>
  <option value="3">linear stereo</option>
</select>
<select id="pitchMode" style="width: 90px;" onchange="">   
  <option value="0">treble (agudo)</option>
  <option value="1">bass (grave)</option>
  <option value="2" selected>treble-bass (agudo-grave)</option>
</select>

<a href="#" onclick="alert('TONE SPEED\nUse the arrow keys to select the high-pitched or low-pitched sound sound\nUse las flechas para seleccionar el sonido agudo y/o grave\nSource: https://github.com/vernetit/toneSpeed\nLicence: MIT\ncontact:robertchalean@gmail.com')">[?]</a>
<span style="float:right;">
  
  [<span id="pasadas"></span>][<span id="ok" style="color: green;"></span>][<span id="error" style="color: red;"></span>][<span id="promedio"></span>][<span id="vel"></span>]
</span>
<br>
<div style="width: 100px; height: 100px; background-color: white; position: fixed; left: 0px; top: 45%;" id="left-sqr">&nbsp;</div>
<div style="width: 100px; height: 100px; background-color: white; position: fixed; right: 0px; top: 45%;" id="right-sqr">&nbsp;</div>
<div style="width: 100px; height: 100px; background-color: white; position: fixed; : 0px; top: 50px; transform: translateX(-50%); margin-left: 50%;" id="up-sqr">&nbsp;</div>
<div style="width: 100px; height: 100px; background-color: white; position: fixed; : 0px; bottom: 50px; transform: translateX(-50%); margin-left: 50%;" id="down-sqr">&nbsp;</div>
<div style="width: 100px; height: 100px; background-color: white; position: fixed; : 0px; top: 45%; transform: translateX(-50%); margin-left: 50%;" id="center-sqr">&nbsp;</div>

<script type="text/javascript">

var synth = new Tone.Synth();
var synth1 = new Tone.Synth();
panner = new Tone.Panner(0).toMaster();
panner1 = new Tone.Panner(-1).toMaster();
panner2 = new Tone.Panner(1).toMaster();

function n(x){ return parseInt($("#"+x).val()) }


inicioV=0;

maxF=4180;
minF=100;
// minF=28;

_r=0;
ok=0;
error=0;
promedio=0;
pasadas=0;
acum=0;
v=0;
canDo=0;

t_ini=0;
t_fin=0;
t_dif=0;

mode=0;

arrayF=[];
arrayP=[];
pitchMode=0;

squareColor="green";
tipoPitch=0;

function start(x){

  if(x==0){ 
    pitchMode=n("pitchMode")
    mode=n("mode")
    inicioV=n("myVelocity")
    ok=0;
    error=0;
    promedio=0;
    pasadas=0;
    acum=0;

    actualiza();


  }

  if(pitchMode==0){ tipoPitch=1; squareColor="green"; } 
  if(pitchMode==1){ tipoPitch=0; squareColor="red"; } 

  if(pitchMode==2){
    tipoPitch=_.random(0,1); // 0 grave 1 agudo
    if(tipoPitch) squareColor="green"; else squareColor="red";
  } 


  if(mode==0){ //linear mono

    _r=_.random(0,1); // 0 izq 1 der

    if(_r){

      for(;;){
        
        b=_.random(minF,maxF)
        a=_.random(minF,b)

        if(b>a && b-a>10) break;
      }

    }else{
       for(;;){
        
        a=_.random(minF,maxF)
        b=_.random(minF,a)

        if(a>b && a-b>10) break;
       }

    }

    canDo=0;

    t_ini = Date.now();

    _r1=_.random(0,1)

    limpiarPantalla();

    if(_r){ //0 izq

      $("#right-sqr").css("background-color",squareColor);
      synth.triggerAttackRelease(b, (inicioV-5)/1000).connect(panner);  

      setTimeout(function(){ 
        $("#right-sqr").css("background-color","white");
        $("#left-sqr").css("background-color",squareColor); 
        synth.triggerAttackRelease(a, (inicioV-5)/1000).connect(panner); 

         if(tipoPitch==0){ _r=!Boolean(_r);  }

        canDo=1;

      },inicioV)

    }else{

      $("#left-sqr").css("background-color",squareColor);
      synth.triggerAttackRelease(a, (inicioV-5)/1000).connect(panner);  

      setTimeout(function(){ 
        $("#left-sqr").css("background-color","white");
        $("#right-sqr").css("background-color",squareColor); 
        synth.triggerAttackRelease(b, (inicioV-5)/1000).connect(panner); 

        if(tipoPitch==0){ _r=!Boolean(_r); }

        canDo=1;

      },inicioV)

    }

    setTimeout(function(){
      limpiarPantalla();

    },inicioV*2);


  }//mode==0

  if(mode==1){ //triangle

    canDo=0;

    t_ini = Date.now();

    arrayF=[];
    for(;;){
      arrayF[0]=_.random(minF,maxF)
      arrayF[1]=_.random(minF,arrayF[0])
      arrayF[2]=_.random(minF,arrayF[1])

      if(arrayF[0]-arrayF[1]>=10 && arrayF[1]-arrayF[2]>=10) break;
    }
    arrayP=_.range(0,3); //izq up der

    arrayF=_.shuffle(arrayF)
    arrayP=_.shuffle(arrayP)

    console.log(arrayP)
    
    setTimeout(function(){ limpiarPantalla(); $("#"+getSquareNameByNum(arrayP[0])).css("background-color",squareColor); synth.triggerAttackRelease(arrayF[0], (inicioV-5)/1000).connect(panner); },inicioV*0)
    setTimeout(function(){ limpiarPantalla(); $("#"+getSquareNameByNum(arrayP[1])).css("background-color",squareColor); synth.triggerAttackRelease(arrayF[1], (inicioV-5)/1000).connect(panner); },inicioV*1)
    setTimeout(function(){ limpiarPantalla(); $("#"+getSquareNameByNum(arrayP[2])).css("background-color",squareColor); synth.triggerAttackRelease(arrayF[2], (inicioV-5)/1000).connect(panner); canDo=1; },inicioV*2)
    setTimeout(function(){ limpiarPantalla(); }, inicioV*3)

    
  }

  if(mode==2){ //triangle

    canDo=0;

    t_ini = Date.now();

    arrayF=[];
    for(;;){
      arrayF[0]=_.random(minF,maxF)
      arrayF[1]=_.random(minF,arrayF[0])
      arrayF[2]=_.random(minF,arrayF[1])
      arrayF[3]=_.random(minF,arrayF[2])

      if(arrayF[0]-arrayF[1]>=10 && arrayF[1]-arrayF[2]>=10 && arrayF[2]-arrayF[3]>=10) break;
    }
    arrayP=_.range(0,4); //izq up der

    arrayF=_.shuffle(arrayF)
    arrayP=_.shuffle(arrayP)

    console.log(arrayP)
    
    setTimeout(function(){ limpiarPantalla(); $("#"+getSquareNameByNum(arrayP[0])).css("background-color",squareColor); synth.triggerAttackRelease(arrayF[0], (inicioV-5)/1000).connect(panner); },inicioV*0)
    setTimeout(function(){ limpiarPantalla(); $("#"+getSquareNameByNum(arrayP[1])).css("background-color",squareColor); synth.triggerAttackRelease(arrayF[1], (inicioV-5)/1000).connect(panner); },inicioV*1)
    setTimeout(function(){ limpiarPantalla(); $("#"+getSquareNameByNum(arrayP[2])).css("background-color",squareColor); synth.triggerAttackRelease(arrayF[2], (inicioV-5)/1000).connect(panner); canDo=1; },inicioV*2)
    setTimeout(function(){ limpiarPantalla(); $("#"+getSquareNameByNum(arrayP[3])).css("background-color",squareColor); synth.triggerAttackRelease(arrayF[3], (inicioV-5)/1000).connect(panner); canDo=1; },inicioV*3)
    setTimeout(function(){ limpiarPantalla(); }, inicioV*4)
    
  }

  if(mode==3){


    _r=_.random(0,1); // 0 izq 1 der

    if(_r){

      for(;;){
        
        b=_.random(minF,maxF)
        a=_.random(minF,b)

        if(b>a && b-a>10) break;
      }

    }else{
       for(;;){
        
        a=_.random(minF,maxF)
        b=_.random(minF,a)

        if(a>b && a-b>10) break;
       }

    }

    canDo=0;

    t_ini = Date.now();

    _r1=_.random(0,1)

    limpiarPantalla();

    if(_r){ //0 izq

      $("#center-sqr").css("background-color",squareColor);
      synth.triggerAttackRelease(b, (inicioV-5)/1000).connect(panner1);  
      synth1.triggerAttackRelease(a, (inicioV-5)/1000).connect(panner2); 

    }else{

      $("#center-sqr").css("background-color",squareColor);
      synth.triggerAttackRelease(a, (inicioV-5)/1000).connect(panner1);  
      synth1.triggerAttackRelease(b, (inicioV-5)/1000).connect(panner2); 

    }

    if(tipoPitch==0){ _r=!Boolean(_r); } canDo=1;

    setTimeout(function(){ limpiarPantalla(); },inicioV*2);

  }

  pasadas++;
 

}  

function getSquareNameByNum(x){
  if(x==0) return "left-sqr"
  if(x==1) return "up-sqr"
  if(x==2) return "right-sqr"
  if(x==3) return "down-sqr"
  if(x==4) return "center-sqr"
}


function limpiarPantalla(){
  $("#left-sqr").css("background-color","white");
  $("#right-sqr").css("background-color","white");
  $("#up-sqr").css("background-color","white");
  $("#down-sqr").css("background-color","white");
  $("#center-sqr").css("background-color","white");
}

function actualiza(){

  $("#pasadas").html(pasadas)
  $("#ok").html(ok)
  $("#error").html(error)
  $("#promedio").html(parseInt(promedio))
  $("#vel").html(parseInt(inicioV))
}

$(document).keydown(function(e) {
  // // prevent the default action (scroll / move caret)
  
  console.log(e.which);

  // console.log(canDo)

  if(pasadas==0) return;
  if(!canDo) return;

  //if(currentEtapa !=0) return;

  // console.log(canDo)

  switch(e.which) {

    case 39:  //right


      if(mode==0 || mode==3){
        if(_r){
          ok++;
          if(inicioV>=n("limit")+10) inicioV-=10;

        }else{

          error++;
          inicioV+=10;
        }


        t_fin = Date.now();
        t_dif = t_fin - t_ini;
        acum+=t_dif;
        promedio=acum/pasadas;

        start(1)
      }

      if(mode==1 || mode==2){
        pos=arrayP.indexOf(2);

        if(tipoPitch==0) coteja=arrayF.min();
        if(tipoPitch==1) coteja=arrayF.max();

        if(arrayF[pos]==coteja){
          ok++;
          if(inicioV>=n("limit")+10) inicioV-=10;

        }else{
          error++;
          inicioV+=10;
        }

        t_fin = Date.now();
        t_dif = t_fin - t_ini;
        acum+=t_dif;
        promedio=acum/pasadas;

        start(1)


      }

      break; 


    case 37:  //left

      if(mode==0 || mode==3){
        if(_r){
          error++;
          inicioV+=10;

        }else{

          ok++;
          if(inicioV>=n("limit")+10) inicioV-=10;
        }

        t_fin = Date.now();
        t_dif = t_fin - t_ini;
        acum+=t_dif;
        promedio=acum/pasadas;

        start(1)


      }

      if(mode==1 || mode==2){
        pos=arrayP.indexOf(0);

        if(tipoPitch==0) coteja=arrayF.min();
        if(tipoPitch==1) coteja=arrayF.max();

        if(arrayF[pos]==coteja){
          ok++;
          if(inicioV>=n("limit")+10) inicioV-=10;

        }else{
          error++;
          inicioV+=10;
        }

        t_fin = Date.now();
        t_dif = t_fin - t_ini;
        acum+=t_dif;
        promedio=acum/pasadas;

        start(1)


      }

     

      break; 

    case 38:  //up
       if(mode==1 || mode==2){

        pos=arrayP.indexOf(1);

        console.log(pos)

        if(tipoPitch==0) coteja=arrayF.min();
        if(tipoPitch==1) coteja=arrayF.max();

        if(arrayF[pos]==coteja){
          ok++;
          if(inicioV>=n("limit")+10) inicioV-=10;

        }else{
          error++;
          inicioV+=10;
        }

        t_fin = Date.now();
        t_dif = t_fin - t_ini;
        acum+=t_dif;
        promedio=acum/pasadas;

        start(1)


      }

      break;

    case 40: //down

      if(mode==2){

        pos=arrayP.indexOf(3);

        console.log(pos)

        if(tipoPitch==0) coteja=arrayF.min();
        if(tipoPitch==1) coteja=arrayF.max();

        if(arrayF[pos]==coteja){
          ok++;
          if(inicioV>=n("limit")+10) inicioV-=10;

        }else{
          error++;
          inicioV+=10;
        }

        t_fin = Date.now();
        t_dif = t_fin - t_ini;
        acum+=t_dif;
        promedio=acum/pasadas;

        start(1)


      }

      break;
  }

  actualiza();
});

Array.prototype.max = function() {
  return Math.max.apply(null, this);
};

Array.prototype.min = function() {
  return Math.min.apply(null, this);
};

actualiza();

</script>
</body>
</html>