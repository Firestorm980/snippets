/**
 * numberFormat
 *
 * Takes a number and adds appropriate commas to it.
 * 
 * @param  {number} x The number to be changed
 * @return {string}   The number with formatting applied
 */
function numberFormat(x){
	var parts = x.toString().split(".");
	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	return parts.join(".");
}