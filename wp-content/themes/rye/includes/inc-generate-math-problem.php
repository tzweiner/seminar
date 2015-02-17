<?php 
$numbers = array (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
//$operators = array ('*', '+');
$operators = array (
		array ('Multiply', 'by'),
		array ('Add', 'to')
		);

$add1 = get_random ($numbers);
$add2 = get_random ($numbers);
$op = get_random ($operators);
//$solution = eval('return '.$add1.$op.$add2.';'); 

?>

	<label for="txt-math">* <?php _e('Solve this simple math problem (prevents spam submissions)', 'rye'); ?><span class="error-math-incorrect"><br />Incorrect solution</span></label>
	<div>
		<div class="form-label margin-bottom"><?php echo $op [0]; ?> <span class="value1"><?php echo $add1; ?></span> <span class="op1"><?php echo $op [1]; ?></span> <span class="value2"><?php echo $add2; ?></span> = </div>
		<input name="txt-math" id="txt-math" type="text" class="required" />
	</div>


<?php 
function get_random ($array) {
	if (is_array ($array)) {
		return $array [ rand (0, (count($array) - 1) ) ];
	}
	
	return false;
}
?>