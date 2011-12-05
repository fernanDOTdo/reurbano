pageTracker._addTrans(
      "order-id", // required
      "affiliate or store name",
      "total",
      "tax",
      "shipping",
      "city",
      "state",
      "country"
); 
 
pageTracker._addItem(
      "order-id", // required
      "SKU",
      "product name",
      "product category",
      "unit price",  // required
      "quantity"  //required
); 
 
pageTracker._trackTrans();