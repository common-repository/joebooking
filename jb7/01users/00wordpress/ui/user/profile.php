<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_01Users_00WordPress_Ui_User_Profile
{
	public function __construct(
		JB7_01Users_Data_Repo $repoUsers,
		HC4_Auth_Interface $auth,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug )
	{
		$currentUserId = $this->auth->getCurrentUserId();
		$user = $this->repoUsers->findById( $currentUserId );

		$return = $this->render( $user );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( JB7_01Users_Data_Model $user )
	{
		ob_start();
?>

<div class="hc4-form-elements">

	<div class="hc4-form-element">
		<label>__Display Name__</label>
		<?php echo $user->title; ?>
	</div>

	<div class="hc4-form-element">
		<label>__Email__</label>
		<?php echo $user->email; ?>
	</div>

	<div class="hc4-form-element">
		<label>__Username__</label>
		<?php echo $user->username; ?>
	</div>

	<div class="hc4-form-element">
		<a href="<?php echo get_edit_user_link(); ?>">__Edit My Profile__</a>
	</div>

</div>

<?php 
		return ob_get_clean();
	}
}