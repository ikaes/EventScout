<?php 

function se_get_admin_text_field($label, $name, $value = "", $class = "", $input_type='text', $readonly = false)
{
	$class = !empty($class) ? ' class="'.$class.'"' : '';
	$readonly = ($readonly) ? ' readonly' : '';
	?>
	<div <?php echo $class; ?>>
		<?php if($input_type != 'hidden'): ?>
		<label for="<?php echo $name; ?>" class="label"><?php echo $label; ?></label>
		<?php endif;?>
		<input type="<?php echo $input_type; ?>" id="<?php echo $name; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $readonly; ?> />
	</div>
    <?php
}

function se_get_admin_textarea_field($label, $name, $value="", $class= "" ) {
	 
	$class = $class ? ' class="'.$class.'"' : ''; 
	?>
	<div <?php echo $class; ?>>
		<label for="<?php echo $name; ?>"><?php echo $label; ?></label>
		<textarea id="<?php echo $name; ?>" name="<?php echo $name; ?>"><?php echo $value; ?></textarea>
	</div>
	<?php
}

function se_get_admin_select_field($label, $optionsArray, $defaultValue = "", $name = "", $classes = "") {
	?>
	<div class="<?php echo $classes; ?>">
		<label for="<?php echo $name; ?>" class="label"><?php echo $label; ?></label>
		<select id="<?php echo $name ?>" name="<?php echo $name; ?>">
			<?php 
				foreach($optionsArray as $value => $title){
					echo "<option value='".$value."' ".(($value==$defaultValue)?"selected":"").">".$title."</option>";
				}
			 ?>
		</select>
	</div>
	<?php 
}

function se_get_admin_readonly_field($label, $name, $hidden_value, $visible_data, $class = "")
{
	$class = !empty($class) ? ' class="'.$class.'"' : '';
	?>
	<div <?php echo $class; ?>>
		<label for="<?php echo $name; ?>" class="label"><?php echo $label; ?></label>
		<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $hidden_value; ?>" />
		<span id="<?php echo $name; ?>" ><?php echo $visible_data; ?></span>
	</div>
    <?php
}



