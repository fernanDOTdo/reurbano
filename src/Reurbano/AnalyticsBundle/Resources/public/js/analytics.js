function analyticsDeal(cl, ln, zn, dl)
{
	$.post("/analytics/deal", { cl: cl, ln: ln, zn: zn, dl: dl }
/*	   
	   function(data) {
	     alert("Data Loaded: " + data);
	   }
*/
	);
};

