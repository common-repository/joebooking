<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Data_Fields_Type_Text
	extends JB7_31Bookings_Data_Fields_Type
{
	public $inputText;

	public function __construct(
		HC4_Html_Input_Text $inputText
	)
	{
		$this->inputText = $inputText;
	}

	public function renderEdit()
	{
		$fname = $this->name;
		$default = isset( $this->details['default'] ) ? $this->details['default'] : NULL;
		ob_start();
?>
	<?php echo $this->inputText->render( $fname, $default ); ?>

<?php 
		return ob_get_clean();
	}

	public function grab( $post )
	{
		$fname = $this->name;

		$errors = array();

		$required = isset( $this->details['required'] ) ? $this->details['required'] : FALSE;
		if( $required ){
			if( ! (isset($post[$fname]) && strlen($post[$fname])) ){
				$errors[$fname] = '__Required Field__';
			}
		}

		if( $errors ){
			throw new HC4_App_Exception_FormErrors( $errors );
		}

		return $post[ $fname ];
	}
}