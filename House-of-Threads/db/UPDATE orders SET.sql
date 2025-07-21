UPDATE orders SET 
  promo_code_temp = promo_code,
  promo_code = address,
  address = promo_code_temp;
