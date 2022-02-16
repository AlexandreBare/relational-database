const TABLE_BORDER = '1';
const TABLE_WIDTH = '250';

/*
 * create an HTML table based on the content of an array
 *
 * Arguments:
 * headers, the array of table headers
 * array, the array to display in the form of an HTML table
 * tableId, the id of the table
 */
 function createTable(headers, array, tableId){
   var table = document.createElement('TABLE');

   table.id = tableId;
   table.border = TABLE_BORDER;

   // TABLE HEADERS
   var tr = document.createElement('TR');
   for (i = 0; i < headers.length; i++) {
       var th = document.createElement('TH');
       th.width = TABLE_WIDTH;
       th.innerHTML = headers[i];
       tr.appendChild(th);
   }
   table.appendChild(tr);

   // TABLE ROWS
   for (i = 0; i < array.length; i++) {
       var tr = document.createElement('TR');
       for (var key in array[i]) {
           var td = document.createElement('TD');
           td.innerHTML = array[i][key];
           tr.appendChild(td);
       }
       table.appendChild(tr);
   }

   return table;
}
