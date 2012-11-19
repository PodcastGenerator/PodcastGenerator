function checkMaxSelected (select, maxSelected, displ_error_nummaxcat) {
	console.log('------------');
  if (!select.storeSelections) {
   select.storeSelections = [];
   select.selectedOptions = 0;
   }
  
   for (var i = 0; i < select.options.length; i++) {
  
  console.log('select.options[i].selected: '+select.options[i].selected+' select.storeSelections[i]: '+select.storeSelections[i]);
   
     if (select.options[i].selected && 
         !select.storeSelections[i]) {
       if (select.selectedOptions[i] < maxSelected) {
         select.storeSelections[i] = true;
         select.selectedOptions++;
       }
      else {
      //  alert(displ_error_nummaxcat + maxSelected);
        console.log('HERE I SHOW ALERT!');
		select.options[i].selected = false;
		console.log('select.options[i].selected: '+select.options[i].selected);
      }
    }
     else if (!select.options[i].selected &&
       select.storeSelections[i]) {
       select.storeSelections[i] = false;
       select.selectedOptions--;
     }
  }
};