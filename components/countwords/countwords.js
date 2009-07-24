function cnt(w,x){
var y=w.value;
var r = 0;
a=y.replace(/\s/g,' ');
a=a.split(',');
for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
x.value=r;
} 
