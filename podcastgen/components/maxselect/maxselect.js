function checkMaxSelected (select, maxSelected, nummaxcat) {
  if (!select.storeSelections) {
    select.storeSelections = new Array(select.options.length);
    select.selectedOptions = 0;
  }
  for (var i = 0; i < select.options.length; i++) {
    if (select.options[i].selected && 
        !select.storeSelections[i]) {
      if (select.selectedOptions < maxSelected) {
        select.storeSelections[i] = true;
        select.selectedOptions++;
      }
      else {
        alert(nummaxcat + maxSelected);
        select.options[i].selected = false;
      }
    }
    else if (!select.options[i].selected &&
             select.storeSelections[i]) {
      select.storeSelections[i] = false;
      select.selectedOptions--;
    }
  }
} 
